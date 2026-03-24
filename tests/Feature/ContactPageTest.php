<?php

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('contact page renders', function () {
    $this->get(route('contact.create'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page->component('Contact/Index'));
});

test('contact form stores message and redirects with flash', function () {
    $payload = [
        'name' => 'Jane Homeowner',
        'email' => 'jane@example.com',
        'subject' => 'Question about booking',
        'message' => 'I need help scheduling a plumber for next week.',
    ];

    $response = $this->post(route('contact.store'), $payload);

    $response->assertRedirect(route('contact.create'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'Jane Homeowner',
        'email' => 'jane@example.com',
        'subject' => 'Question about booking',
        'message' => 'I need help scheduling a plumber for next week.',
    ]);

    expect(ContactMessage::query()->count())->toBe(1);
});

test('contact form validation errors', function () {
    $response = $this->post(route('contact.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);
});
