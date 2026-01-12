<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::check()) {
            $user = Auth::user();
            $tenant = $user->getCurrentTenant();
            
            if ($tenant) {
                $tenantName = $tenant->name;
                $tenantLogo = $tenant->logo_path ? asset($tenant->logo_path) : null;
                $primaryColor = $tenant->primary_color ?? '#007bff';
                $secondaryColor = $tenant->secondary_color ?? '#6c757d';
            }
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
