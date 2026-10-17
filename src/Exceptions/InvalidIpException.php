<?php

namespace Spatie\Rdap\Exceptions;

use Exception;

/** @phpstan-consistent-constructor */
class InvalidIpException extends Exception
{
    public static function make(string $ip): self
    {
        return new static("The IP `{$ip}` is not valid.");
    }
}
