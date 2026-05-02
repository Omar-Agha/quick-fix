<?php

namespace App\Core\SmsProviders;

use App\Core\SmsProviders\ISmsProvider;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class MoceanSmsProvider implements ISmsProvider
{
    // Implement the methods defined in ISmsProvider here.

    public function sendSms(string $to, string $message): bool
    {

        Log::debug('MoceanSmsProvider: Attempting to send SMS', [
            'to' => $to,
            'message' => $message,
        ]);

        $token = config('services.mocean.token');

        $from = config('services.mocean.from');

        $endpoint = config('services.mocean.endpoint');

        if (blank($token)) {

            throw new RuntimeException('Mocean API token is not configured.');
        }

        if (blank($from)) {

            throw new RuntimeException('Mocean sender ID is not configured.');
        }

        if (blank($to)) {

            throw new RuntimeException('SMS receiver number is required.');
        }

        if (blank($message)) {

            throw new RuntimeException('SMS message text is required.');
        }

        // Mocean expects international format without spaces.

        // Example: 966563060083, 963940921165, 628xxxxxxxxxx

        $to = $this->normalizePhoneNumber($to);

        try {

            $response = Http::asForm()

                ->withToken($token)

                ->timeout(15)

                ->retry(2, 500)

                ->post($endpoint, [

                    'mocean-from' => $from,

                    'mocean-to' => $to,

                    'mocean-text' => $message,

                    'mocean-resp-format' => 'JSON',

                ]);

            if ($response->failed()) {

                throw new RuntimeException(

                    "Mocean HTTP error {$response->status()}: {$response->body()}"

                );
            }

            $data = $response->json();

            if (! is_array($data)) {

                throw new RuntimeException('Invalid Mocean response: ' . $response->body());
            }

            $messageResult = data_get($data, 'messages.0');

            if (! $messageResult) {

                throw new RuntimeException('Mocean response does not contain message result.');
            }

            $status = (string) data_get($messageResult, 'status');

            if ($status !== '0') {

                throw new RuntimeException(

                    'Mocean SMS failed: ' . $this->statusMessage($status),

                    (int) $status

                );
            }
            return true;
            // return [

            //     'success' => true,

            //     'receiver' => data_get($messageResult, 'receiver'),

            //     'message_id' => data_get($messageResult, 'msgid'),

            //     'status' => $status,

            // ];
        } catch (RequestException $e) {

            Log::error('Mocean SMS request exception', [

                'to' => $to,

                'message' => $e->getMessage(),

            ]);

            throw new RuntimeException('Failed to send SMS through Mocean.', 0, $e);
        } catch (Throwable $e) {

            Log::error('Mocean SMS error', [

                'to' => $to,

                'error' => $e->getMessage(),

            ]);

            throw $e;
        }
    }
    private function normalizePhoneNumber(string $number): string

    {

        // Remove spaces, plus sign, hyphens, and brackets.

        $number = preg_replace('/[^\d]/', '', $number);

        if (strlen($number) < 8) {

            throw new RuntimeException('Invalid phone number format.');
        }

        return $number;
    }

    private function statusMessage(string $status): string

    {

        return match ($status) {

            '1' => 'Authorization failed. Check API token.',

            '2' => 'Insufficient balance.',

            '4' => 'Destination number is not whitelisted.',

            '5' => 'Destination number is blacklisted.',

            '6' => 'No destination number specified.',

            '8' => 'Sender ID not found or not approved.',

            '9' => 'Invalid UDH field.',

            '10' => 'Invalid mclass field.',

            default => "Unknown Mocean error status: {$status}",
        };
    }
}
