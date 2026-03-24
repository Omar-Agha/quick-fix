<?php

test('about us page returns successful response', function () {
    $response = $this->get(route('about'));

    $response->assertSuccessful();
});
