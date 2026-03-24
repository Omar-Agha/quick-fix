<?php

use Inertia\Testing\AssertableInertia as Assert;

test('download fallback page renders', function () {
    $response = $this->get(route('download'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page->component('Download'));
});
