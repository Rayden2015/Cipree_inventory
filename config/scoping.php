<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site scope bypass roles
    |--------------------------------------------------------------------------
    |
    | By default, the application is tenant-scoped AND site-scoped.
    | Some roles (e.g. Tenant Admin) may need to see all sites within a tenant.
    |
    | Add role names here to bypass the global site_id filter.
    | You can also override via env SITE_SCOPE_BYPASS_ROLES="Tenant Admin,Super Admin,My New Role"
    |
    */
    'site_bypass_roles' => array_values(array_filter(array_map('trim', explode(',', env(
        'SITE_SCOPE_BYPASS_ROLES',
        'Tenant Admin,Super Admin'
    ))))),
];

