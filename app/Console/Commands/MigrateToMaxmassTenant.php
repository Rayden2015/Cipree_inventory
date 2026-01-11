<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\Site;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MigrateToMaxmassTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-to-maxmass {--force : Force migration even if some data already has tenant_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all existing users and data to Maxmass tenant (for pre-multi-tenancy data)';

    /**
     * Tables that need tenant_id assignment
     */
    protected $tablesWithTenantId = [
        'orders',
        'porders',
        'sorders',
        'inventory_items',
        'inventories',
        'items',
        'departments',
        'sections',
        'locations',
        'suppliers',
        'endusers',
        'end_users_categories',
        'categories',
        'companies',
        'purchases',
        'order_parts',
        'porder_parts',
        'sorder_parts',
        'stock_purchase_requests',
        'spr_porders',
        'spr_porder_items',
        'inventory_item_details',
        'taxes',
        'levies',
        'notifications',
        'logins',
        'parts',
        'employees',
        'uoms',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting migration to Maxmass tenant...');
        $this->newLine();

        // Check if migrations have been run (tenant_id columns exist)
        $migrationsRun = $this->checkMigrationsRun();
        if (!$migrationsRun) {
            $this->error('❌ Multi-tenancy migrations have not been run yet!');
            $this->error('   Please run: php artisan migrate');
            $this->error('   Then run this command again.');
            return Command::FAILURE;
        }

        // Check if data already has tenant_id
        if (!$this->option('force')) {
            $hasTenantData = $this->checkExistingTenantData();
            if ($hasTenantData) {
                $this->warn('⚠️  Some data already has tenant_id assigned.');
                $this->warn('   Use --force flag to proceed anyway.');
                return Command::FAILURE;
            }
        }

        try {
            DB::beginTransaction();

            // Step 1: Create or get Maxmass tenant
            $this->info('📦 Step 1: Creating Maxmass tenant...');
            $maxmassTenant = $this->createMaxmassTenant();
            $this->info("   ✅ Tenant created/found: {$maxmassTenant->name} (ID: {$maxmassTenant->id})");
            $this->newLine();

            // Step 2: Create or get Head Office site
            $this->info('🏢 Step 2: Creating Head Office site...');
            $headOfficeSite = $this->createHeadOfficeSite($maxmassTenant);
            $this->info("   ✅ Site created/found: {$headOfficeSite->name} (ID: {$headOfficeSite->id})");
            $this->newLine();

            // Step 3: Update sites
            $this->info('📍 Step 3: Updating sites...');
            $sitesUpdated = $this->updateSites($maxmassTenant);
            $this->info("   ✅ Updated {$sitesUpdated} sites");
            $this->newLine();

            // Step 4: Update users
            $this->info('👥 Step 4: Updating users...');
            $usersUpdated = $this->updateUsers($maxmassTenant, $headOfficeSite);
            $this->info("   ✅ Updated {$usersUpdated} users");
            $this->newLine();

            // Step 5: Update data tables
            $this->info('📊 Step 5: Updating data tables...');
            $this->updateDataTables($maxmassTenant);
            $this->info("   ✅ All data tables updated");
            $this->newLine();

            DB::commit();

            $this->info('✅ Migration completed successfully!');
            $this->newLine();
            $this->info('Summary:');
            $this->info("   - Tenant: {$maxmassTenant->name} (ID: {$maxmassTenant->id})");
            $this->info("   - Head Office Site: {$headOfficeSite->name} (ID: {$headOfficeSite->id})");
            $this->info("   - Sites updated: {$sitesUpdated}");
            $this->info("   - Users updated: {$usersUpdated}");
            $this->newLine();

            Log::info('MigrateToMaxmassTenant | Migration completed', [
                'tenant_id' => $maxmassTenant->id,
                'site_id' => $headOfficeSite->id,
                'sites_updated' => $sitesUpdated,
                'users_updated' => $usersUpdated,
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Migration failed: ' . $e->getMessage());
            $this->error('   File: ' . $e->getFile());
            $this->error('   Line: ' . $e->getLine());
            
            Log::error('MigrateToMaxmassTenant | Migration failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Check if migrations have been run (tenant_id columns exist)
     */
    protected function checkMigrationsRun(): bool
    {
        // Check if tenants table exists
        if (!Schema::hasTable('tenants')) {
            return false;
        }

        // Check if tenant_id columns exist in key tables
        $keyTables = ['users', 'sites'];
        foreach ($keyTables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'tenant_id')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if any data already has tenant_id assigned
     */
    protected function checkExistingTenantData(): bool
    {
        // Check users table - only if column exists
        if (Schema::hasColumn('users', 'tenant_id')) {
            if (User::whereNotNull('tenant_id')->exists()) {
                $this->warn('   Found users with tenant_id');
                return true;
            }
        }

        // Check sites table - only if column exists
        if (Schema::hasColumn('sites', 'tenant_id')) {
            if (Site::whereNotNull('tenant_id')->exists()) {
                $this->warn('   Found sites with tenant_id');
                return true;
            }
        }

        // Check a few key tables - only if column exists
        foreach (['orders', 'items', 'inventories'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                if (DB::table($table)->whereNotNull('tenant_id')->exists()) {
                    $this->warn("   Found {$table} records with tenant_id");
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Create or get Maxmass tenant
     */
    protected function createMaxmassTenant(): Tenant
    {
        $tenant = Tenant::where('slug', 'maxmass')
            ->orWhere('name', 'Maxmass')
            ->first();

        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'Maxmass',
                'slug' => 'maxmass',
                'status' => 'Active',
                'description' => 'Default tenant for existing Maxmass data',
            ]);
        }

        return $tenant;
    }

    /**
     * Create or get Head Office site for Maxmass
     */
    protected function createHeadOfficeSite(Tenant $tenant): Site
    {
        $query = Site::where('name', 'Head Office');
        
        // Only filter by tenant_id if the column exists
        if (Schema::hasColumn('sites', 'tenant_id')) {
            $query->where('tenant_id', $tenant->id);
        }
        
        $site = $query->first();

        if (!$site) {
            $siteData = [
                'name' => 'Head Office',
                'site_code' => 'HO-MAX',
            ];
            
            // Only add tenant_id if column exists
            if (Schema::hasColumn('sites', 'tenant_id')) {
                $siteData['tenant_id'] = $tenant->id;
            }
            
            $site = Site::create($siteData);
        }

        return $site;
    }

    /**
     * Update all sites to belong to Maxmass tenant
     */
    protected function updateSites(Tenant $tenant): int
    {
        // Check if tenant_id column exists
        if (!Schema::hasColumn('sites', 'tenant_id')) {
            $this->warn('   ⚠️  tenant_id column does not exist in sites table. Run migrations first.');
            return 0;
        }

        return Site::whereNull('tenant_id')
            ->update(['tenant_id' => $tenant->id]);
    }

    /**
     * Update all users to belong to Maxmass tenant and Head Office site
     */
    protected function updateUsers(Tenant $tenant, Site $headOfficeSite): int
    {
        // Check if tenant_id column exists
        if (!Schema::hasColumn('users', 'tenant_id')) {
            $this->warn('   ⚠️  tenant_id column does not exist in users table. Run migrations first.');
            return 0;
        }

        $updated = 0;

        // Update users without tenant_id
        $usersWithoutTenant = User::whereNull('tenant_id')->get();

        foreach ($usersWithoutTenant as $user) {
            // Skip Super Admin users (they should have tenant_id = null)
            if ($user->isSuperAdmin()) {
                continue;
            }

            // If user has a site_id, use that site's tenant_id (if site has tenant_id)
            // Otherwise, assign to Head Office
            if ($user->site_id) {
                $userSite = Site::find($user->site_id);
                if ($userSite && Schema::hasColumn('sites', 'tenant_id') && $userSite->tenant_id) {
                    // Site already has tenant_id, use that
                    $user->tenant_id = $userSite->tenant_id;
                } else {
                    // Site doesn't have tenant_id yet, assign user to Maxmass and keep their site
                    $user->tenant_id = $tenant->id;
                    // Ensure the site also belongs to Maxmass (if column exists)
                    if ($userSite && Schema::hasColumn('sites', 'tenant_id')) {
                        $userSite->tenant_id = $tenant->id;
                        $userSite->save();
                    }
                }
            } else {
                // User has no site, assign to Maxmass tenant and Head Office site
                $user->tenant_id = $tenant->id;
                $user->site_id = $headOfficeSite->id;
            }

            $user->save();
            $updated++;
        }

        return $updated;
    }

    /**
     * Update all data tables to have tenant_id
     */
    protected function updateDataTables(Tenant $tenant): void
    {
        $bar = $this->output->createProgressBar(count($this->tablesWithTenantId));
        $bar->start();

        foreach ($this->tablesWithTenantId as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $bar->advance();
                continue;
            }

            if (!DB::getSchemaBuilder()->hasColumn($table, 'tenant_id')) {
                $bar->advance();
                continue;
            }

            // Strategy: Update based on site_id if available, otherwise use tenant_id directly
            if (DB::getSchemaBuilder()->hasColumn($table, 'site_id')) {
                // For tables with site_id, get tenant_id from the site
                DB::statement("
                    UPDATE {$table} t
                    INNER JOIN sites s ON t.site_id = s.id
                    SET t.tenant_id = s.tenant_id
                    WHERE t.tenant_id IS NULL AND s.tenant_id IS NOT NULL
                ");

                // For records with site_id but site doesn't have tenant_id, assign to Maxmass
                DB::statement("
                    UPDATE {$table} t
                    INNER JOIN sites s ON t.site_id = s.id
                    SET t.tenant_id = ?
                    WHERE t.tenant_id IS NULL AND s.tenant_id IS NULL
                ", [$tenant->id]);

                // For records without site_id, assign to Maxmass
                DB::table($table)
                    ->whereNull('tenant_id')
                    ->whereNull('site_id')
                    ->update(['tenant_id' => $tenant->id]);
            } else {
                // For tables without site_id, assign directly to Maxmass
                DB::table($table)
                    ->whereNull('tenant_id')
                    ->update(['tenant_id' => $tenant->id]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }
}
