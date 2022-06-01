<?php

use Spatie\Rdap\Facades\Rdap;
use Spatie\Rdap\Responses\DomainResponse;

it('has a facade to work with rdap', function() {
    $response = Rdap::domain('google.com');

    expect($response)->toBeInstanceOf(DomainResponse::class);
});
