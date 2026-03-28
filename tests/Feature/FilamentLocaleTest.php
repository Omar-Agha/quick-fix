<?php

test('filament admin routes use panel locale from config', function () {
    session(['locale' => 'id']);

    $this->get('/admin/login')->assertSuccessful();

    expect(app()->getLocale())->toBe(config('filament.panel_locale'));
});

test('public site keeps session locale when not filament', function () {
    session(['locale' => 'id']);

    $this->get(route('home'))->assertSuccessful();

    expect(app()->getLocale())->toBe('id');
});
