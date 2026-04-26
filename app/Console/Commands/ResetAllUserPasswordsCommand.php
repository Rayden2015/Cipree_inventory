<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAllUserPasswordsCommand extends Command
{
    protected $signature = 'users:reset-all-passwords
                            {--password=password : The plaintext password to set for every user}
                            {--force : Required to actually perform the update}';

    protected $description = 'Reset every user password to a single value (DANGEROUS).';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $this->error('Refusing to run without --force.');
            $this->line('Example: php artisan users:reset-all-passwords --password=password --force');
            return self::FAILURE;
        }

        if (app()->environment('production')) {
            $this->warn('YOU ARE IN PRODUCTION.');
            $this->warn('This will reset EVERY user password in the database.');

            if (! $this->confirm('Are you sure you want to continue?', false)) {
                $this->info('Cancelled.');
                return self::SUCCESS;
            }
        }

        $plain = (string) $this->option('password');
        $hashed = Hash::make($plain);

        $updated = User::query()->update(['password' => $hashed]);

        $this->info("Updated {$updated} users.");
        return self::SUCCESS;
    }
}

