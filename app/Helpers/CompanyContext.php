<?php

namespace App\Helpers;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class CompanyContext
{
    /**
     * Return the "current" company record for the active tenant context.
     * In multi-tenant mode, NEVER use Company::first() (it leaks across tenants).
     */
    public static function current(): ?Company
    {
        $user = Auth::user();

        $tenantId = session('current_tenant_id') ?? $user?->getCurrentTenant()?->id;
        if (! $tenantId) {
            return Company::query()->latest()->first();
        }

        $siteId = $user?->site?->id;

        return Company::query()
            ->where('tenant_id', $tenantId)
            ->when($siteId, fn ($q) => $q->where(function ($sub) use ($siteId) {
                $sub->whereNull('site_id')->orWhere('site_id', $siteId);
            }))
            ->latest()
            ->first();
    }
}

