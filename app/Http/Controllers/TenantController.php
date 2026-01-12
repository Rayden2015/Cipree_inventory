<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\Site;
use App\Models\User;
use App\Helpers\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;

class TenantController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Only Super Admin can access tenant management
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isSuperAdmin()) {
                Toastr::error('You do not have permission to access this page.');
                return redirect()->route('home');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of tenants (Super Admin only)
     */
    public function index(Request $request)
    {
        try {
            Log::info('TenantController | index() | Fetching tenants list', [
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email ?? 'unknown',
                'filters' => $request->all(),
            ]);

            $query = Tenant::withCount(['sites', 'users'])
                ->latest();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('domain', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%")
                        ->orWhere('contact_email', 'like', "%{$search}%");
                });
            }

            // Status filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $tenants = $query->paginate(15)->withQueryString();

            // Get statistics for the view
            $stats = [
                'total' => Tenant::count(),
                'active' => Tenant::where('status', 'Active')->count(),
                'inactive' => Tenant::where('status', 'Inactive')->count(),
                'suspended' => Tenant::where('status', 'Suspended')->count(),
            ];

            Log::info('TenantController | index() | Tenants fetched successfully', [
                'count' => $tenants->total(),
                'current_page' => $tenants->currentPage(),
            ]);

            return view('tenants.index', compact('tenants', 'stats'));
        } catch (\Exception $e) {
            Log::error('TenantController | index() | Error fetching tenants', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => Auth::id(),
            ]);
            Toastr::error('An error occurred while fetching tenants.');
            return redirect()->route('home');
        }
    }

    /**
     * Show the form for creating a new tenant
     */
    public function create()
    {
        return view('tenants.create');
    }

    /**
     * Store a newly created tenant in storage
     */
    public function store(Request $request)
    {
        // Log the attempt
        Log::info('TenantController | store() | Tenant creation attempt started', [
            'user_id' => Auth::id(),
            'user_email' => Auth::user()->email ?? 'unknown',
            'request_data' => $request->except(['admin_password', 'admin_password_confirmation', '_token']),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tenants,slug',
            'domain' => 'nullable|string|max:255|unique:tenants,domain',
            'description' => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive,Suspended',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            DB::beginTransaction();

            // Create tenant
            $tenantData = [
                'name' => $request->name,
                'slug' => $request->slug ?? Str::slug($request->name),
                'domain' => $request->domain,
                'description' => $request->description,
                'contact_name' => $request->contact_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'status' => $request->status,
                'primary_color' => $request->primary_color ?? '#007bff',
                'secondary_color' => $request->secondary_color,
            ];

            Log::info('TenantController | store() | Creating tenant', [
                'tenant_data' => $tenantData,
            ]);

            $tenant = Tenant::create($tenantData);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                try {
                    $uploadDir = public_path('images/tenants');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $logoName = UploadHelper::upload($request->logo, 'tenant-' . $tenant->id, $uploadDir);
                    $tenant->logo_path = 'images/tenants/' . $logoName;
                    $tenant->save();
                    
                    Log::info('TenantController | store() | Logo uploaded', [
                        'tenant_id' => $tenant->id,
                        'logo_path' => $tenant->logo_path
                    ]);
                } catch (\Exception $e) {
                    Log::error('TenantController | store() | Logo upload failed', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue - tenant created but logo upload failed
                }
            }

            if (!$tenant || !$tenant->id) {
                throw new \Exception('Failed to create tenant - no ID returned');
            }

            Log::info('TenantController | store() | Tenant created', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
            ]);

            // Commit tenant creation first to avoid GTID consistency issues
            DB::commit();

            // Create default "Head Office" site for the tenant
            DB::beginTransaction();
            
            // Generate a unique site code: HO-{first 3 chars of slug}-{first 3 chars of tenant ID}
            $siteCode = 'HO-' . strtoupper(substr($tenant->slug, 0, 3));
            if (strlen($siteCode) < 6) {
                $siteCode = 'HO-' . strtoupper(substr($tenant->slug, 0, min(5, strlen($tenant->slug))));
            }
            
            $defaultSite = Site::create([
                'name' => 'Head Office',
                'site_code' => $siteCode,
                'tenant_id' => $tenant->id,
            ]);

            if (!$defaultSite || !$defaultSite->id) {
                DB::rollBack();
                try {
                    $tenant->delete();
                    Log::info('TenantController | store() | Tenant deleted due to default site creation failure', [
                        'tenant_id' => $tenant->id,
                    ]);
                } catch (\Exception $deleteException) {
                    Log::error('TenantController | store() | Failed to delete tenant after site creation failure', [
                        'tenant_id' => $tenant->id,
                        'error' => $deleteException->getMessage(),
                    ]);
                }
                throw new \Exception('Failed to create default site for tenant');
            }

            Log::info('TenantController | store() | Default site created', [
                'site_id' => $defaultSite->id,
                'site_name' => $defaultSite->name,
                'tenant_id' => $tenant->id,
            ]);

            DB::commit();

            // Start new transaction for user creation
            DB::beginTransaction();

            // Create tenant admin user and assign to default site
            $userData = [
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'tenant_id' => $tenant->id,
                'site_id' => $defaultSite->id, // Assign to default Head Office site
                'status' => 'Active',
            ];

            Log::info('TenantController | store() | Creating tenant admin user', [
                'user_data' => array_merge($userData, ['password' => '[HIDDEN]']),
            ]);

            $tenantAdmin = User::create($userData);

            if (!$tenantAdmin || !$tenantAdmin->id) {
                DB::rollBack();
                // Delete the tenant and default site that were created if user creation fails
                try {
                    $defaultSite->delete();
                    $tenant->delete();
                    Log::info('TenantController | store() | Tenant and default site deleted due to user creation failure', [
                        'tenant_id' => $tenant->id,
                        'site_id' => $defaultSite->id,
                    ]);
                } catch (\Exception $deleteException) {
                    Log::error('TenantController | store() | Failed to delete tenant/site after user creation failure', [
                        'tenant_id' => $tenant->id,
                        'site_id' => $defaultSite->id ?? null,
                        'error' => $deleteException->getMessage(),
                    ]);
                }
                throw new \Exception('Failed to create tenant admin user - no ID returned');
            }

            // Commit user creation before assigning role (to avoid GTID consistency issues)
            DB::commit();

            // Assign Tenant Admin role (this may write to non-transactional tables)
            // Do this outside transaction to avoid GTID consistency errors
            try {
                $tenantAdmin->assignRole('Tenant Admin');

                Log::info('TenantController | store() | Tenant Admin role assigned', [
                    'user_id' => $tenantAdmin->id,
                    'role' => 'Tenant Admin',
                ]);
            } catch (\Exception $roleException) {
                // Log but don't fail - user is created, role can be assigned manually if needed
                Log::error('TenantController | store() | Failed to assign role', [
                    'error' => $roleException->getMessage(),
                    'user_id' => $tenantAdmin->id,
                    'tenant_id' => $tenant->id,
                ]);
                // Delete user and tenant if role assignment is critical
                // For now, we'll just log it and continue
            }

            // Log to file
            Log::info('TenantController | store() | Tenant created successfully', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'tenant_slug' => $tenant->slug,
                'default_site_id' => $defaultSite->id,
                'default_site_name' => $defaultSite->name,
                'admin_user_id' => $tenantAdmin->id,
                'admin_email' => $tenantAdmin->email,
                'created_by' => Auth::id(),
                'created_by_email' => Auth::user()->email ?? 'unknown',
            ]);

            // Log to audit table
            $this->logToAuditTable('create', 'tenants', $tenant->id, [
                'name' => $tenant->name,
                'slug' => $tenant->slug,
                'domain' => $tenant->domain,
                'status' => $tenant->status,
            ]);

            // Log default site creation to audit table
            $this->logToAuditTable('create', 'sites', $defaultSite->id, [
                'name' => $defaultSite->name,
                'site_code' => $defaultSite->site_code,
                'tenant_id' => $defaultSite->tenant_id,
            ], 'Default Head Office site created for tenant: ' . $tenant->name);

            // Also log user creation to audit table
            $this->logToAuditTable('create', 'users', $tenantAdmin->id, [
                'name' => $tenantAdmin->name,
                'email' => $tenantAdmin->email,
                'tenant_id' => $tenantAdmin->tenant_id,
                'site_id' => $tenantAdmin->site_id,
                'role' => 'Tenant Admin',
            ], 'Tenant Admin user created for tenant: ' . $tenant->name . ' (assigned to Head Office)');

            Toastr::success('Tenant and Tenant Admin created successfully.');
            return redirect()->route('tenants.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Rollback any active transaction
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('TenantController | store() | Validation error', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);
            throw $e; // Re-throw to let Laravel handle validation errors
        } catch (\Exception $e) {
            // Rollback any active transaction
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('TenantController | store() | Error creating tenant', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'error_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'user_email' => Auth::user()->email ?? 'unknown',
            ]);
            Toastr::error('An error occurred while creating the tenant: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified tenant
     */
    public function show($id)
    {
        try {
            $tenant = Tenant::with(['tenantAdmins', 'sites', 'users'])
                ->findOrFail($id);

            return view('tenants.show', compact('tenant'));
        } catch (\Exception $e) {
            Log::error('TenantController | show() | Error: ' . $e->getMessage());
            Toastr::error('Tenant not found.');
            return redirect()->route('tenants.index');
        }
    }

    /**
     * Show the form for editing the specified tenant
     */
    public function edit($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);
            return view('tenants.edit', compact('tenant'));
        } catch (\Exception $e) {
            Log::error('TenantController | edit() | Error: ' . $e->getMessage());
            Toastr::error('Tenant not found.');
            return redirect()->route('tenants.index');
        }
    }

    /**
     * Update the specified tenant in storage
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tenants,slug,' . $id,
            'domain' => 'nullable|string|max:255|unique:tenants,domain,' . $id,
            'description' => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive,Suspended',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            $tenant = Tenant::findOrFail($id);
            
            // Store old values for audit log
            $oldValues = $tenant->only([
                'name', 'slug', 'domain', 'description',
                'contact_name', 'contact_email', 'contact_phone', 'status',
                'logo_path', 'primary_color', 'secondary_color'
            ]);

            $updateData = $request->only([
                'name', 'slug', 'domain', 'description',
                'contact_name', 'contact_email', 'contact_phone', 'status',
                'primary_color', 'secondary_color'
            ]);
            
            // Handle logo upload
            if ($request->hasFile('logo')) {
                try {
                    $uploadDir = public_path('images/tenants');
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $logoName = UploadHelper::update(
                        $request->logo, 
                        'tenant-' . $tenant->id, 
                        $uploadDir, 
                        $tenant->logo_path ? basename($tenant->logo_path) : null
                    );
                    $updateData['logo_path'] = 'images/tenants/' . $logoName;
                    
                    Log::info('TenantController | update() | Logo uploaded', [
                        'tenant_id' => $tenant->id,
                        'logo_path' => $updateData['logo_path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('TenantController | update() | Logo upload failed', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $tenant->update($updateData);

            // Log to file
            Log::info('TenantController | update() | Tenant updated', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'updated_by' => Auth::id(),
                'updated_by_email' => Auth::user()->email ?? 'unknown',
                'changes' => $tenant->getChanges(),
            ]);

            // Log to audit table
            $this->logToAuditTable('update', 'tenants', $tenant->id, $tenant->getChanges(), 'Tenant updated', $oldValues);

            Toastr::success('Tenant updated successfully.');
            return redirect()->route('tenants.index');

        } catch (\Exception $e) {
            Log::error('TenantController | update() | Error: ' . $e->getMessage(), [
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'tenant_id' => $id,
                'user_id' => Auth::id(),
            ]);
            Toastr::error('An error occurred while updating the tenant.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified tenant from storage
     */
    public function destroy($id)
    {
        try {
            $tenant = Tenant::findOrFail($id);

            // Prevent deletion if tenant has active sites or users
            if ($tenant->sites()->count() > 0 || $tenant->users()->count() > 0) {
                Toastr::error('Cannot delete tenant with existing sites or users.');
                return redirect()->back();
            }

            // Store tenant data before deletion for audit log
            $tenantData = $tenant->toArray();

            $tenant->delete();

            // Log to file
            Log::info('TenantController | destroy() | Tenant deleted', [
                'tenant_id' => $id,
                'tenant_name' => $tenantData['name'] ?? 'unknown',
                'deleted_by' => Auth::id(),
                'deleted_by_email' => Auth::user()->email ?? 'unknown',
            ]);

            // Log to audit table
            $this->logToAuditTable('delete', 'tenants', $id, [], 'Tenant deleted', $tenantData);

            Toastr::success('Tenant deleted successfully.');
            return redirect()->route('tenants.index');

        } catch (\Exception $e) {
            Log::error('TenantController | destroy() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred while deleting the tenant.');
            return redirect()->back();
        }
    }

    /**
     * Create a new tenant admin for a tenant
     */
    public function createTenantAdmin($tenantId)
    {
        try {
            $tenant = Tenant::findOrFail($tenantId);
            return view('tenants.create-admin', compact('tenant'));
        } catch (\Exception $e) {
            Toastr::error('Tenant not found.');
            return redirect()->route('tenants.index');
        }
    }

    /**
     * Store a new tenant admin
     */
    public function storeTenantAdmin(Request $request, $tenantId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $tenant = Tenant::findOrFail($tenantId);

            $tenantAdmin = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id,
                'site_id' => null,
                'status' => 'Active',
            ]);

            $tenantAdmin->assignRole('Tenant Admin');

            Log::info('TenantController | storeTenantAdmin() | Tenant Admin created', [
                'tenant_id' => $tenant->id,
                'admin_user_id' => $tenantAdmin->id,
                'created_by' => Auth::id()
            ]);

            // Log to file
            Log::info('TenantController | storeTenantAdmin() | Tenant Admin created', [
                'tenant_id' => $tenant->id,
                'admin_user_id' => $tenantAdmin->id,
                'admin_email' => $tenantAdmin->email,
                'created_by' => Auth::id(),
            ]);

            // Log to audit table
            $this->logToAuditTable('create', 'users', $tenantAdmin->id, [
                'name' => $tenantAdmin->name,
                'email' => $tenantAdmin->email,
                'tenant_id' => $tenantAdmin->tenant_id,
                'role' => 'Tenant Admin',
            ], 'Additional Tenant Admin created for tenant: ' . $tenant->name);

            Toastr::success('Tenant Admin created successfully.');
            return redirect()->route('tenants.show', $tenant->id);

        } catch (\Exception $e) {
            Log::error('TenantController | storeTenantAdmin() | Error: ' . $e->getMessage(), [
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'tenant_id' => $tenantId,
                'user_id' => Auth::id(),
            ]);
            Toastr::error('An error occurred while creating the tenant admin.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Log action to audit table
     * Works with existing audit_logs table structure
     */
    private function logToAuditTable(string $action, string $tableName, ?int $recordId, array $newValues = [], ?string $description = null, array $oldValues = []): void
    {
        try {
            if (!Schema::hasTable('audit_logs')) {
                Log::warning('TenantController | logToAuditTable() | audit_logs table does not exist');
                return;
            }

            // Get existing columns to build flexible insert
            $columns = Schema::getColumnListing('audit_logs');
            $data = [];

            // Build data array based on what columns exist
            if (in_array('action', $columns)) {
                $data['action'] = $action;
            }
            if (in_array('table_name', $columns)) {
                $data['table_name'] = $tableName;
            }
            if (in_array('record_id', $columns)) {
                $data['record_id'] = $recordId;
            }
            if (in_array('user_id', $columns)) {
                $data['user_id'] = Auth::id();
            }
            if (in_array('user_email', $columns)) {
                $data['user_email'] = Auth::user()->email ?? null;
            }
            if (in_array('old_values', $columns)) {
                $data['old_values'] = !empty($oldValues) ? json_encode($oldValues) : null;
            }
            if (in_array('new_values', $columns)) {
                $data['new_values'] = !empty($newValues) ? json_encode($newValues) : null;
            }
            if (in_array('description', $columns)) {
                $data['description'] = $description ?? ucfirst($action) . ' ' . $tableName . ' (ID: ' . $recordId . ')';
            }
            if (in_array('ip_address', $columns)) {
                $data['ip_address'] = request()->ip();
            }
            if (in_array('user_agent', $columns)) {
                $data['user_agent'] = request()->userAgent();
            }
            if (in_array('created_at', $columns)) {
                $data['created_at'] = now();
            }
            if (in_array('updated_at', $columns)) {
                $data['updated_at'] = now();
            }

            // Insert only if we have data
            if (!empty($data)) {
                DB::table('audit_logs')->insert($data);
                Log::debug('TenantController | logToAuditTable() | Audit log inserted successfully', [
                    'action' => $action,
                    'table_name' => $tableName,
                    'record_id' => $recordId,
                ]);
            } else {
                Log::warning('TenantController | logToAuditTable() | No matching columns found in audit_logs table', [
                    'available_columns' => $columns ?? [],
                ]);
            }
        } catch (\Exception $e) {
            // Don't fail the main operation if audit logging fails
            Log::error('TenantController | logToAuditTable() | Failed to log to audit table', [
                'error' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'action' => $action,
                'table_name' => $tableName,
                'record_id' => $recordId,
            ]);
        }
    }

    /**
     * Display Super Admin dashboard
     */
    public function dashboard()
    {
        // Ensure only Super Admins can access
        if (!Auth::user()->isSuperAdmin()) {
            Toastr::error('You do not have permission to access this page.');
            return redirect()->route('home');
        }
        
        try {
            // Get overall statistics
            $totalTenants = Tenant::count();
            $activeTenants = Tenant::where('status', 'Active')->count();
            $inactiveTenants = Tenant::where('status', 'Inactive')->count();
            $suspendedTenants = Tenant::where('status', 'Suspended')->count();
            
            // Get total users across all tenants
            $totalUsers = User::whereNotNull('tenant_id')->count();
            
            // Get total sites across all tenants
            $totalSites = Site::whereNotNull('tenant_id')->count();
            
            // Get tenant admins count
            $totalTenantAdmins = User::whereHas('roles', function ($query) {
                $query->where('name', 'Tenant Admin');
            })->whereNotNull('tenant_id')->count();
            
            // Get recent tenants
            $recentTenants = Tenant::withCount(['sites', 'users'])
                ->latest()
                ->take(5)
                ->get();
            
            // Get tenants with most users
            $topTenantsByUsers = Tenant::withCount(['users', 'sites'])
                ->orderBy('users_count', 'desc')
                ->take(5)
                ->get();
            
            // Get tenants with most sites
            $topTenantsBySites = Tenant::withCount(['sites', 'users'])
                ->orderBy('sites_count', 'desc')
                ->take(5)
                ->get();
            
            // Get growth metrics (last 30 days)
            $tenantsCreatedLastMonth = Tenant::where('created_at', '>=', now()->subDays(30))->count();
            $usersCreatedLastMonth = User::whereNotNull('tenant_id')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            $sitesCreatedLastMonth = Site::whereNotNull('tenant_id')
                ->where('created_at', '>=', now()->subDays(30))
                ->count();
            
            // Get tenants created this month
            $tenantsCreatedThisMonth = Tenant::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            // Calculate active percentage
            $activePercentage = $totalTenants > 0 ? round(($activeTenants / $totalTenants) * 100, 1) : 0;
            
            // Get recently created tenants (last 7 days)
            $recentlyCreatedTenants = Tenant::where('created_at', '>=', now()->subDays(7))
                ->withCount(['sites', 'users'])
                ->latest()
                ->get();

            $stats = [
                'total_tenants' => $totalTenants,
                'active_tenants' => $activeTenants,
                'inactive_tenants' => $inactiveTenants,
                'suspended_tenants' => $suspendedTenants,
                'total_users' => $totalUsers,
                'total_sites' => $totalSites,
                'total_tenant_admins' => $totalTenantAdmins,
                'tenants_created_last_month' => $tenantsCreatedLastMonth,
                'users_created_last_month' => $usersCreatedLastMonth,
                'sites_created_last_month' => $sitesCreatedLastMonth,
                'tenants_created_this_month' => $tenantsCreatedThisMonth,
                'active_percentage' => $activePercentage,
            ];

            return view('super-admin.dashboard', compact('stats', 'recentTenants', 'topTenantsByUsers', 'topTenantsBySites', 'recentlyCreatedTenants'));
        } catch (\Exception $e) {
            Log::error('TenantController | dashboard() | Error: ' . $e->getMessage(), [
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => Auth::id(),
            ]);
            Toastr::error('An error occurred while loading the dashboard.');
            return redirect()->route('home');
        }
    }
}
