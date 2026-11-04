<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects guests from the root to the admin login', function () {
    $this->get('/')->assertRedirect(route('admin.login'));
});
