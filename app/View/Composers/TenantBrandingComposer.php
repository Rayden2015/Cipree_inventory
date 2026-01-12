<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\UserController;

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
        $defaultLogo = asset('images/company/' . UserController::logo());

        if (Auth::check()) {
            $user = Auth::user();
            $tenant = $user->getCurrentTenant();
            
            if ($tenant) {
                $tenantName = $tenant->name;
                
                // Check if tenant has a logo and the file exists
                if ($tenant->logo_path) {
                    $logoPath = public_path($tenant->logo_path);
                    if (File::exists($logoPath)) {
                        $tenantLogo = asset($tenant->logo_path);
                    } else {
                        // Logo path in DB but file doesn't exist, use default
                        $tenantLogo = $defaultLogo;
                    }
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
