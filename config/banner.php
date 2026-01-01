<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Banner Enabled
    |--------------------------------------------------------------------------
    |
    | Set this to true to enable the banner popup, false to disable it.
    | When disabled, the banner will not show at all.
    |
    */

    'enabled' => env('BANNER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Service Disable Date
    |--------------------------------------------------------------------------
    |
    | The date when the service will be disabled/moved out of testing phase.
    | Format: Y-m-d (e.g., '2026-01-01')
    | This date will be displayed in the banner message.
    |
    */

    'disable_date' => env('BANNER_DISABLE_DATE', '2026-01-01'),

    /*
    |--------------------------------------------------------------------------
    | Banner Dismissible
    |--------------------------------------------------------------------------
    |
    | Set this to false to make the banner permanent (non-dismissible).
    | When false, users cannot close or dismiss the banner.
    | When true, users can dismiss the banner (original behavior).
    |
    */

    'dismissible' => env('BANNER_DISMISSIBLE', false),

];

