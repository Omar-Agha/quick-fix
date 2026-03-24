<?php

it('provides English ui translation strings', function () {
    app()->setLocale('en');

    expect(__('ui.nav.home'))->toBe('Home');
    expect(__('ui.contact.form.success'))->toBe('Thank you for your message. We will get back to you soon.');
});

it('provides Indonesian ui translation strings', function () {
    app()->setLocale('id');

    expect(__('ui.nav.home'))->toBe('Beranda');
    expect(__('ui.contact.form.success'))->toBe('Terima kasih atas pesan Anda. Kami akan segera menghubungi Anda.');
});

it('loads the home page successfully', function () {
    $this->get('/')->assertOk();
});
