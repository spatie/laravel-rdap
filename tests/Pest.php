<?php

use Carbon\Carbon;
use Spatie\Rdap\Tests\TestSupport\TestCase;

uses(TestCase::class)->in(__DIR__);

expect()->extend('toBeDate', function(string $expectedDate) {
    expect($this->value)->toBeInstanceOf(Carbon::class);

    expect($this->value->format('Y-m-d H:i:s'))->toBe($expectedDate);
});
