@php
    use App\Models\Enduser;
    use App\Models\WorkOrder;

    $user = Auth::user();
    $siteId = $user->site->id ?? null;
    $tenantId = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;

    // Assets (equipment / machines)
    $assetsBaseQuery = Enduser::query()
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->whereIn('type', ['Equipment', 'Machine']);

    // Total assets in scope
    $totalAssets = $assetsBaseQuery->count();

    // Assets currently marked as Operational
    $operationalAssets = (clone $assetsBaseQuery)
        ->where('status', 'Operational')
        ->count();

    $assetAvailability = $totalAssets > 0
        ? round(100 * ($operationalAssets / $totalAssets), 1)
        : null;

    // Active Work Orders (Open / In Progress / Standby)
    $activeWorkOrdersCount = WorkOrder::query()
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->whereIn('status', ['Open', 'In Progress', 'Standby'])
        ->count();

    // Emergency Alerts (Critical priority)
    $emergencyAlertsCount = WorkOrder::query()
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->where('priority', 'Critical')
        ->whereIn('status', ['Open', 'In Progress'])
        ->count();

    // Critical Queue (reactive)
    $criticalQueue = WorkOrder::query()
        ->with(['asset', 'responsiblePerson'])
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->where('priority', 'Critical')
        ->whereIn('status', ['Open', 'In Progress', 'Standby'])
        ->latest('requested_date')
        ->take(10)
        ->get();

    // Pending work orders (Open)
    $pendingWorkOrders = WorkOrder::query()
        ->with(['asset', 'responsiblePerson'])
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->where('status', 'Open')
        ->latest('requested_date')
        ->take(10)
        ->get();

    // Top 5 active work orders (Open / In Progress / Standby)
    $topActiveWorkOrders = WorkOrder::query()
        ->with(['asset', 'responsiblePerson'])
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->whereIn('status', ['Open', 'In Progress', 'Standby'])
        ->latest('requested_date')
        ->take(5)
        ->get();
@endphp

<div class="container-fluid">
    <!-- Top-Level KPI Ribbon -->
    <div class="row">
        <div class="col-lg-4 col-12">
            <div class="small-box bg-success" id="rcorners1">
                <div class="inner">
                    <h4>{{ $assetAvailability !== null ? $assetAvailability . '%' : 'N/A' }}</h4>
                    <p>Asset Availability</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box bg-info" id="rcorners1">
                <div class="inner">
                    <h4>{{ $activeWorkOrdersCount }}</h4>
                    <p>Active Work Orders</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clipboard"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-12">
            <div class="small-box bg-danger emergency" id="rcorners1">
                <div class="inner">
                    <h4>{{ $emergencyAlertsCount }}</h4>
                    <p>Emergency Alerts</p>
                </div>
                <div class="icon">
                    <i class="ion ion-alert"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Queue -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3 class="card-title">Critical Queue (Reactive)</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Asset ID</th>
                            <th>WO #</th>
                            <th>Status</th>
                            <th>Downtime</th>
                            <th>Assigned Tech</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($criticalQueue as $wo)
                            <tr class="{{ $wo->priority === 'Critical' ? 'emergency' : '' }}">
                                <td>{{ optional($wo->asset)->asset_staff_id ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('work-orders.show', $wo) }}">{{ $wo->work_order_number }}</a>
                                </td>
                                <td>{{ $wo->status }}</td>
                                <td>
                                    @php
                                        $downSince = $wo->asset_down_since ?? $wo->requested_date;
                                    @endphp
                                    @if($downSince)
                                        <span class="downtime-clock"
                                              data-start="{{ $downSince->toIso8601String() }}">
                                            {{ $downSince->diffForHumans(null, true) }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ optional($wo->responsiblePerson)->name_description ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No critical work orders.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Work Orders -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Pending Work Orders</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>WO #</th>
                            <th>Asset</th>
                            <th>Responsible</th>
                            <th>Requested</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pendingWorkOrders as $wo)
                            <tr>
                                <td>
                                    <a href="{{ route('work-orders.show', $wo) }}">
                                        {{ $wo->work_order_number }}
                                    </a>
                                </td>
                                <td>{{ optional($wo->asset)->asset_staff_id ?? 'N/A' }}</td>
                                <td>{{ optional($wo->responsiblePerson)->name_description ?? 'N/A' }}</td>
                                <td>{{ optional($wo->requested_date)->format('d-M-Y') ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No pending work orders.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top 5 Active Work Orders -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">Top 5 Active Work Orders</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>WO #</th>
                            <th>Asset</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Responsible</th>
                            <th>Requested</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($topActiveWorkOrders as $wo)
                            <tr>
                                <td>
                                    <a href="{{ route('work-orders.show', $wo) }}">
                                        {{ $wo->work_order_number }}
                                    </a>
                                </td>
                                <td>{{ optional($wo->asset)->asset_staff_id ?? 'N/A' }}</td>
                                <td>{{ $wo->status }}</td>
                                <td>{{ $wo->priority }}</td>
                                <td>{{ optional($wo->responsiblePerson)->name_description ?? 'N/A' }}</td>
                                <td>{{ optional($wo->requested_date)->format('d-M-Y') ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No active work orders found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('work-orders.create') }}?priority=Critical" class="btn btn-danger mr-2">
                        [+] Create Emergency Work Order
                    </a>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-outline-primary mr-2">
                        View All Work Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .emergency {
        background-color: #FF0000 !important;
        color: #FFFFFF !important;
    }
</style>

<script>
    function updateDowntimeClocks() {
        const elements = document.querySelectorAll('.downtime-clock');
        const now = new Date();
        elements.forEach(el => {
            const startStr = el.getAttribute('data-start');
            if (!startStr) return;
            const start = new Date(startStr);
            const diffMs = now - start;
            const diffMins = Math.floor(diffMs / 60000);
            const days = Math.floor(diffMins / (60 * 24));
            const hours = Math.floor((diffMins % (60 * 24)) / 60);
            const minutes = diffMins % 60;
            let text = '';
            if (days > 0) text += days + 'd ';
            if (hours > 0 || days > 0) text += hours + 'h ';
            text += minutes + 'm';
            el.textContent = text;
        });
    }

    updateDowntimeClocks();
    setInterval(updateDowntimeClocks, 60000);
</script>

