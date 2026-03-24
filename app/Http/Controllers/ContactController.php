<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Settings\ContactInfoSettings;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Contact/Index', [
            'email' => app(ContactInfoSettings::class)->email,
            'phone' => app(ContactInfoSettings::class)->phone,
            'address' => app(ContactInfoSettings::class)->address,
        ]);
        // return Inertia::render('Contact/Index');
    }

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::query()->create($request->validated());

        return redirect()
            ->route('contact.create')
            ->with('success', __('Thank you for your message. We will get back to you soon.'));
    }
}
