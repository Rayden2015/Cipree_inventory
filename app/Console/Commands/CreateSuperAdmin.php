<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-super-admin {email} {name?} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Super Admin user for multi-tenancy management';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $name = $this->argument('name') ?? 'Super Admin';
        
        // Check if user already exists
        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists!");
            return Command::FAILURE;
        }

        // Get or prompt for password
        $password = $this->option('password');
        if (!$password) {
            $password = $this->secret('Enter password for Super Admin (min 8 characters):');
            $passwordConfirm = $this->secret('Confirm password:');
            
            if ($password !== $passwordConfirm) {
                $this->error('Passwords do not match!');
                return Command::FAILURE;
            }
            
            if (strlen($password) < 8) {
                $this->error('Password must be at least 8 characters long!');
                return Command::FAILURE;
            }
        }

        try {
            // Create Super Admin user (no tenant_id or site_id - Super Admin is global)
            $superAdmin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'status' => 'Active',
                'tenant_id' => null,
                'site_id' => null,
            ]);

            // Ensure Super Admin role exists
            $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin']);
            
            // Assign Super Admin role
            $superAdmin->assignRole($superAdminRole);

            $this->info("✅ Super Admin created successfully!");
            $this->info("Email: {$email}");
            $this->info("Name: {$name}");
            $this->info("Role: Super Admin");
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Error creating Super Admin: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
