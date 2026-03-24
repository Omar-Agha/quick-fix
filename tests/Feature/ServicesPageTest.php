<?php

use App\Models\Service;
use Inertia\Testing\AssertableInertia as Assert;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('services index page renders', function () {
    $response = $this->get(route('services.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Services/Index')
        ->has('services', 0));
});

test('services index lists active services only', function () {
    Service::factory()->count(2)->create();
    Service::factory()->inactive()->create();

    $response = $this->get(route('services.index'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Services/Index')
        ->has('services', 2));
});
