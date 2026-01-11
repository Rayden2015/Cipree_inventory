<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    /**
     * Handle an incoming request.
     * Set tenant context for the authenticated user
     * In production, validates domain against tenant's domain
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isProduction = app()->environment('production');
        $host = $request->getHost();
        
        // In production, check domain-based tenant routing (except for Super Admin)
        if ($isProduction && Auth::check()) {
            $user = Auth::user();
            
            // Super Admin bypasses domain check
            if (!$user->isSuperAdmin()) {
                // Find tenant by domain
                $tenantByDomain = Tenant::where('domain', $host)
                    ->orWhere('domain', 'www.' . $host)
                    ->orWhere('domain', str_replace('www.', '', $host))
                    ->first();
                
                if ($tenantByDomain) {
                    // Verify user belongs to this tenant
                    $userTenant = $user->getCurrentTenant();
                    
                    if (!$userTenant || $userTenant->id !== $tenantByDomain->id) {
                        Log::warning('TenantContext | Domain-tenant mismatch', [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'requested_domain' => $host,
                            'tenant_domain' => $tenantByDomain->domain,
                            'user_tenant_id' => $userTenant->id ?? null,
                            'domain_tenant_id' => $tenantByDomain->id,
                        ]);
                        
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        return redirect()->route('login')
                            ->withErrors(['email' => 'Access denied. This domain is not associated with your account.']);
                    }
                    
                    // Set tenant context based on domain
                    session(['current_tenant_id' => $tenantByDomain->id]);
                    
                    Log::debug('TenantContext | Domain-based tenant routing', [
                        'domain' => $host,
                        'tenant_id' => $tenantByDomain->id,
                        'tenant_name' => $tenantByDomain->name,
                        'user_id' => $user->id,
                    ]);
                } else {
                    // Domain not found in tenants table - log warning but allow (for flexibility)
                    Log::warning('TenantContext | Domain not found in tenants', [
                        'domain' => $host,
                        'user_id' => $user->id,
                    ]);
                }
            }
        }
        
        // Standard tenant context setting (for both production and local)
        if (Auth::check()) {
            $user = Auth::user();
            
            // Super Admin can access all tenants (no tenant restriction)
            if ($user->isSuperAdmin()) {
                // Allow Super Admin to switch tenant context via query parameter if needed
                if ($request->has('tenant_id')) {
                    $tenantId = $request->get('tenant_id');
                    $tenant = Tenant::find($tenantId);
                    if ($tenant) {
                        session(['current_tenant_id' => $tenantId]);
                    }
                } elseif (!session()->has('current_tenant_id')) {
                    // No tenant context set, proceed without restriction
                    session(['current_tenant_id' => null]);
                }
            } else {
                // For non-Super Admins, set tenant context based on user's tenant
                // (unless already set by domain check above)
                if (!session()->has('current_tenant_id')) {
                    $tenant = $user->getCurrentTenant();
                    
                    if (!$tenant) {
                        Log::warning('TenantContext | User has no tenant assigned', [
                            'user_id' => $user->id,
                            'user_email' => $user->email,
                            'tenant_id' => $user->tenant_id,
                            'site_id' => $user->site_id
                        ]);
                        
                        // If user has no tenant, they can't access the system
                        if (!$user->isSuperAdmin()) {
                            Auth::logout();
                            $request->session()->invalidate();
                            $request->session()->regenerateToken();
                            return redirect()->route('login')
                                ->withErrors(['email' => 'Your account is not assigned to a tenant. Please contact the administrator.']);
                        }
                    } else {
                        session(['current_tenant_id' => $tenant->id]);
                    }
                }
            }
        }

        return $next($request);
    }
}
