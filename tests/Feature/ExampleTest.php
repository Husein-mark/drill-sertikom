<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');
    $response->assertRedirect(route('catalog.index'));

    $catalogResponse = $this->get(route('catalog.index'));
    $catalogResponse->assertStatus(200);
});
