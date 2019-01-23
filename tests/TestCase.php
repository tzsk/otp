<?php

namespace Tzsk\Otp\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return ['Tzsk\Otp\Providers\OtpServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Otp' => 'Tzsk\Otp\Facade\Otp'
        ];
    }
}
