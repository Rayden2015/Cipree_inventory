<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Models\Tenant;

class TenantBrandingComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $tenant = null;
        $tenantLogo = null;
        $tenantName = 'CIPREE';
        $primaryColor = '#007bff';
        $secondaryColor = '#6c757d';

        // Get default logo path
        // Prefer a stable built-in default (multi-tenant safe).
        $defaultLogo = asset('images/branding/cipree.png');

        if (Auth::check()) {
            $user = Auth::user();

            // Prefer the active tenant context (session) over the user's default tenant.
            // This matters for domain-based routing and any future tenant-switching behavior.
            $tenantId = session('current_tenant_id');
            if ($tenantId) {
                $tenant = Tenant::find($tenantId);
            } else {
                $tenant = $user->getCurrentTenant();
            }
            
            if ($tenant) {
                $tenantName = $tenant->name;
                
                if ($tenant->logo_path) {
                    // Don't block on filesystem checks (can be unreliable across deploys/storage setups).
                    $tenantLogo = asset($tenant->logo_path);
                } else {
                    // No logo uploaded, use default
                    $tenantLogo = $defaultLogo;
                }
                
                $primaryColor = $tenant->primary_color ?? '#007bff';
                $secondaryColor = $tenant->secondary_color ?? '#6c757d';
            } else {
                // No tenant, use default logo
                $tenantLogo = $defaultLogo;
            }
        } else {
            // Not authenticated, use default logo
            $tenantLogo = $defaultLogo;
        }

        $view->with([
            'tenantBranding' => [
                'tenant' => $tenant,
                'logo' => $tenantLogo,
                'name' => $tenantName,
                'primary_color' => $primaryColor,
                'secondary_color' => $secondaryColor,
            ]
        ]);
    }
}
