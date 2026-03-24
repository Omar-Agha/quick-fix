<?php

test('locale switch sets session and redirects back', function () {
    $response = $this->from(route('home'))
        ->get(route('locale.switch', ['locale' => 'en']));

    $response->assertRedirect();
    expect(session('locale'))->toBe('en');
});

test('locale switch accepts indonesian', function () {
    $this->from(route('home'))
        ->get(route('locale.switch', ['locale' => 'id']))
        ->assertRedirect();

    expect(session('locale'))->toBe('id');
});

test('unsupported locale returns not found', function () {
    $this->from(route('home'))
        ->get(route('locale.switch', ['locale' => 'fr']))
        ->assertNotFound();
});
