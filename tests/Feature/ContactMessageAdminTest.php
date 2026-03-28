<?php

use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Filament\Resources\ContactMessages\Pages\ManageContactMessages;
use App\Models\ContactMessage;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('viewing contact message in admin marks it read', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    $message = ContactMessage::query()->create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'subject' => 'Hello',
        'message' => 'Body',
    ]);

    expect($message->read_at)->toBeNull();

    Filament::setCurrentPanel('admin');

    Livewire::actingAs($user)
        ->test(ManageContactMessages::class)
        ->mountTableAction('view', $message)
        ->assertSuccessful();

    expect($message->fresh()->read_at)->not->toBeNull();
});

test('navigation badge is null when all messages are read', function () {
    ContactMessage::query()->create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'subject' => 'Hello',
        'message' => 'Body',
        'read_at' => now(),
    ]);

    expect(ContactMessageResource::getNavigationBadge())->toBeNull();
});

test('navigation badge shows unread count', function () {
    ContactMessage::query()->create([
        'name' => 'Test',
        'email' => 'test@example.com',
        'subject' => 'Hello',
        'message' => 'Body',
    ]);

    expect(ContactMessageResource::getNavigationBadge())->toBe('1');
});
