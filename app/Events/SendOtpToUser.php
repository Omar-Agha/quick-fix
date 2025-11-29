<?php

namespace App\Events;

use App\Models\MobileUser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendOtpToUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public MobileUser $user;
    public string $otp;

    /**
     * Create a new event instance.
     */
    public function __construct(MobileUser $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }
}
