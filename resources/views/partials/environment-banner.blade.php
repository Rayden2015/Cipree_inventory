@php
    $env = config('app.env');
    $isProduction = strtolower($env ?? '') === 'production';
@endphp

@if (! $isProduction)
    <div class="env-banner alert alert-warning text-center mb-0" style="border-radius:0;">
        <strong>Non-production environment:</strong>
        You are using a {{ strtoupper($env ?? 'UNKNOWN') }} system. Data here is for testing only.
    </div>
@endif

