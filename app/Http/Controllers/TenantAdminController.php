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
use Brian2694\Toastr\Facades\Toastr;

class TenantAdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Only Tenant Admin and Super Admin can access
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user->isTenantAdmin() && !$user->isSuperAdmin()) {
                Toastr::error('You do not have permission to access this page.');
                return redirect()->route('home');
            }
            return $next($request);
        });
    }

    /**
     * Display tenant admin dashboard
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();
            $tenant = $user->getCurrentTenant();

            if (!$tenant) {
                Toastr::error('You are not assigned to a tenant.');
                return redirect()->route('home');
            }

            $stats = [
                'sites_count' => $tenant->sites()->count(),
                'users_count' => $tenant->users()->count(),
                'active_sites' => $tenant->sites()->where('tenant_id', $tenant->id)->count(),
            ];

            $recentSites = $tenant->sites()->latest()->take(5)->get();
            $recentUsers = $tenant->users()->latest()->take(5)->get();

            return view('tenant-admin.dashboard', compact('tenant', 'stats', 'recentSites', 'recentUsers'));
        } catch (\Exception $e) {
            Log::error('TenantAdminController | dashboard() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred while loading the dashboard.');
            return redirect()->route('home');
        }
    }

    /**
     * Show tenant settings form
     */
    public function settings()
    {
        try {
            $user = Auth::user();
            $tenant = $user->getCurrentTenant();

            if (!$tenant) {
                Toastr::error('You are not assigned to a tenant.');
                return redirect()->route('home');
            }

            return view('tenant-admin.settings', compact('tenant'));
        } catch (\Exception $e) {
            Log::error('TenantAdminController | settings() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred.');
            return redirect()->route('home');
        }
    }

    /**
     * Update tenant settings
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->getCurrentTenant();

        if (!$tenant) {
            Toastr::error('You are not assigned to a tenant.');
            return redirect()->route('home');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'settings' => 'nullable|array',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            $updateData = $request->only([
                'name', 'description', 'contact_name', 'contact_email', 'contact_phone', 'settings',
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
                    
                    Log::info('TenantAdminController | updateSettings() | Logo uploaded', [
                        'tenant_id' => $tenant->id,
                        'logo_path' => $updateData['logo_path']
                    ]);
                } catch (\Exception $e) {
                    Log::error('TenantAdminController | updateSettings() | Logo upload failed', [
                        'tenant_id' => $tenant->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            $tenant->update($updateData);

            Log::info('TenantAdminController | updateSettings() | Tenant settings updated', [
                'tenant_id' => $tenant->id,
                'updated_by' => $user->id
            ]);

            Toastr::success('Tenant settings updated successfully.');
            return redirect()->route('tenant-admin.settings');

        } catch (\Exception $e) {
            Log::error('TenantAdminController | updateSettings() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred while updating settings.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display all sites for the tenant
     */
    public function sites(Request $request)
    {
        try {
            $user = Auth::user();
            $tenant = $user->getCurrentTenant();

            if (!$tenant) {
                Toastr::error('You are not assigned to a tenant.');
                return redirect()->route('home');
            }

            $query = $tenant->sites()->withCount('users')->latest();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('site_code', 'like', "%{$search}%");
                });
            }

            $sites = $query->paginate(15)->withQueryString();

            // Get statistics
            $stats = [
                'total' => $tenant->sites()->count(),
                'total_users' => $tenant->users()->count(),
            ];

            return view('tenant-admin.sites.index', compact('sites', 'tenant', 'stats'));
        } catch (\Exception $e) {
            Log::error('TenantAdminController | sites() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred.');
            return redirect()->route('home');
        }
    }

    /**
     * Show form to create a new site
     */
    public function createSite()
    {
        try {
            $user = Auth::user();
            $tenant = $user->getCurrentTenant();

            if (!$tenant) {
                Toastr::error('You are not assigned to a tenant.');
                return redirect()->route('home');
            }

            return view('tenant-admin.sites.create', compact('tenant'));
        } catch (\Exception $e) {
            Log::error('TenantAdminController | createSite() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred.');
            return redirect()->route('tenant-admin.sites.index');
        }
    }

    /**
     * Store a new site
     */
    public function storeSite(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->getCurrentTenant();

        if (!$tenant) {
            Toastr::error('You are not assigned to a tenant.');
            return redirect()->route('home');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'site_code' => 'nullable|string|max:255',
        ]);

        try {
            $site = Site::create([
                'name' => $request->name,
                'site_code' => $request->site_code,
                'tenant_id' => $tenant->id,
            ]);

            Log::info('TenantAdminController | storeSite() | Site created', [
                'site_id' => $site->id,
                'tenant_id' => $tenant->id,
                'created_by' => $user->id
            ]);

            Toastr::success('Site created successfully.');
            return redirect()->route('tenant-admin.sites.index');

        } catch (\Exception $e) {
            Log::error('TenantAdminController | storeSite() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred while creating the site.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Display all users for the tenant
     */
    public function users(Request $request)
    {
        try {
            $user = Auth::user();
            $tenant = $user->getCurrentTenant();

            if (!$tenant) {
                Toastr::error('You are not assigned to a tenant.');
                return redirect()->route('home');
            }

            $query = $tenant->users()->with(['site', 'roles'])->latest();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Site filter
            if ($request->filled('site_id') && $request->site_id !== 'all') {
                $query->where('site_id', $request->site_id);
            }

            // Status filter
            if ($request->filled('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            $users = $query->paginate(15)->withQueryString();
            $sites = $tenant->sites()->get();

            // Get statistics
            $stats = [
                'total' => $tenant->users()->count(),
                'active' => $tenant->users()->where('status', 'Active')->count(),
                'inactive' => $tenant->users()->where('status', 'Inactive')->count(),
            ];

            return view('tenant-admin.users.index', compact('users', 'tenant', 'sites', 'stats'));
        } catch (\Exception $e) {
            Log::error('TenantAdminController | users() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred.');
            return redirect()->route('home');
        }
    }

    /**
     * Show form to create a new user
     */
    public function createUser()
    {
        try {
            $user = Auth::user();
            $tenant = $user->getCurrentTenant();

            if (!$tenant) {
                Toastr::error('You are not assigned to a tenant.');
                return redirect()->route('home');
            }

            $sites = $tenant->sites()->get();

            return view('tenant-admin.users.create', compact('tenant', 'sites'));
        } catch (\Exception $e) {
            Log::error('TenantAdminController | createUser() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred.');
            return redirect()->route('tenant-admin.users.index');
        }
    }

    /**
     * Store a new user
     */
    public function storeUser(Request $request)
    {
        $user = Auth::user();
        $tenant = $user->getCurrentTenant();

        if (!$tenant) {
            Toastr::error('You are not assigned to a tenant.');
            return redirect()->route('home');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'site_id' => 'nullable|exists:sites,id',
            'status' => 'required|in:Active,Inactive',
        ]);

        try {
            // Verify site belongs to tenant
            if ($request->site_id) {
                $site = Site::where('id', $request->site_id)
                    ->where('tenant_id', $tenant->id)
                    ->first();
                
                if (!$site) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['site_id' => 'Selected site does not belong to your tenant.']);
                }
            }

            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id,
                'site_id' => $request->site_id,
                'status' => $request->status,
            ]);

            // Assign roles if provided
            if ($request->has('roles')) {
                $newUser->assignRole($request->roles);
            }

            Log::info('TenantAdminController | storeUser() | User created', [
                'user_id' => $newUser->id,
                'tenant_id' => $tenant->id,
                'created_by' => $user->id
            ]);

            Toastr::success('User created successfully.');
            return redirect()->route('tenant-admin.users.index');

        } catch (\Exception $e) {
            Log::error('TenantAdminController | storeUser() | Error: ' . $e->getMessage());
            Toastr::error('An error occurred while creating the user.');
            return redirect()->back()->withInput();
        }
    }
}
