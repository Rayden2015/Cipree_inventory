<?php

namespace App\Console\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand;

/**
 * Same behavior as Laravel's db:seed, but with an explicit production warning
 * before the confirmation prompt (still respects --force).
 */
class ProductionAwareSeedCommand extends SeedCommand
{
    public function confirmToProceed($warning = 'Application In Production', $callback = null)
    {
        $warning = 'Database seeding in production can create or update permissions, assign permissions to roles, insert reference data (for example inventory correction codes), and run SuperAdminSeeder. Take a backup first unless you are certain.';

        return parent::confirmToProceed($warning, $callback);
    }
}
