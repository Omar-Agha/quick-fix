<?php

use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

test('welcome page includes empty offers when none published', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->has('offers', 0));
});

test('welcome page passes active published offers', function () {
    Offer::factory()->count(2)->create();
    Offer::factory()->inactive()->create();
    Offer::factory()->draft()->create();

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->has('offers', 2));
});
