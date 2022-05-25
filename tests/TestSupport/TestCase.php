<?php

namespace Spatie\Rdap\Tests\TestSupport;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelRay\RayServiceProvider;
use Spatie\Rdap\RdapServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            RayServiceProvider::class,
            RdapServiceProvider::class,
        ];
    }

    protected function getJsonStub(string $name): array
    {
        $fileName = __DIR__ . "/stubs/{$name}.json";

        $content = file_get_contents($fileName);

        return json_decode($content, true);
    }
}
