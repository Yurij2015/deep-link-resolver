<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

if (function_exists('uses')) {
    uses(Tests\TestCase::class, RefreshDatabase::class)->in('Feature');

    expect()->extend('toBeOne', function () {
        return $this->toBe(1);
    });
}

function something()
{
    // ..
}
