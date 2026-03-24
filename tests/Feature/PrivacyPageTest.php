<?php

use Inertia\Testing\AssertableInertia as Assert;

test('privacy policy page renders', function () {
    $response = $this->get(route('privacy'));

    $response->assertSuccessful();
    $response->assertInertia(fn (Assert $page) => $page->component('Privacy'));
});
