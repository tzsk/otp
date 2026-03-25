<?php

namespace Tzsk\Otp\Commands;

use Illuminate\Console\Command;

class OtpPublishCommand extends Command
{
    public $signature = 'otp:publish';

    public $description = 'Publish OTP config file';

    public function handle(): int
    {
        $this->call('vendor:publish', ['--tag' => 'otp-config']);

        return self::SUCCESS;
    }
}
