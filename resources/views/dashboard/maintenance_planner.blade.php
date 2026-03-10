@php
    use App\Models\Enduser;
    use App\Models\WorkOrder;
    use App\Models\Sorder;
    use App\Models\InventoryCorrectionRequest;

    $user = Auth::user();
    $siteId = $user->site->id ?? null;
    $tenantId = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;

    // Assets (equipment / machines)
    $assetsQuery = Enduser::query()
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->whereIn('type', ['Equipment', 'Machine']);
    $totalAssets = $assetsQuery->count();

    // Assets with active work orders (Open / In Progress)
    $assetsWithActiveWo = WorkOrder::query()
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->whereIn('status', ['Open', 'In Progress'])
        ->distinct('asset_enduser_id')
        ->count('asset_enduser_id');

    $assetAvailability = $totalAssets > 0
        ? round(100 * (1 - ($assetsWithActiveWo / $totalAssets)), 1)
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

    // PM Compliance – placeholder until PM data model is implemented
    $pmCompliance = null;

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

    // Pending correction requests
    $pendingCorrections = InventoryCorrectionRequest::query()
        ->with(['inventoryItem.item', 'requestedByUser'])
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->where('status', InventoryCorrectionRequest::STATUS_PENDING)
        ->latest()
        ->take(10)
        ->get();

    // Open spares requests (SRs not yet supplied)
    $openSparesRequests = Sorder::query()
        ->with(['enduser'])
        ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
        ->when($siteId, fn($q) => $q->where('site_id', $siteId))
        ->where('status', '!=', 'Supplied')
        ->latest('created_at')
        ->take(10)
        ->get();
@endphp

<div class="container-fluid">
    <!-- Top-Level KPI Ribbon -->
    <div class="row">
        <div class="col-lg-3 col-6">
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
        <div class="col-lg-3 col-6">
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
        <div class="col-lg-3 col-6">
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
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary" id="rcorners1">
                <div class="inner">
                    <h4>{{ $pmCompliance !== null ? $pmCompliance . '%' : 'N/A' }}</h4>
                    <p>PM Compliance (Coming Soon)</p>
                </div>
                <div class="icon">
                    <i class="ion ion-clock"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Operations -->
    <div class="row">
        <div class="col-lg-6">
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
                                    @if($wo->requested_date)
                                        <span class="downtime-clock"
                                              data-start="{{ $wo->requested_date->toIso8601String() }}">
                                            {{ $wo->requested_date->diffForHumans(null, true) }}
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

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-warning">
                    <h3 class="card-title">PM Forecast (Coming in v3.0)</h3>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        PM Forecast requires asset usage logs (Hours/KM) and PM schedule thresholds.
                        Once available, this panel will show a traffic-light list of assets approaching service.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Integration Hub -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Pending Correction Requests</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Requested By</th>
                            <th>Intended Qty</th>
                            <th>Notes</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($pendingCorrections as $cr)
                            <tr>
                                <td>{{ optional(optional($cr->inventoryItem)->item)->item_description ?? 'N/A' }}</td>
                                <td>{{ optional($cr->requestedByUser)->name ?? 'N/A' }}</td>
                                <td>{{ $cr->intended_quantity }}</td>
                                <td>{{ Str::limit($cr->notes, 40) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No pending corrections.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3 class="card-title">Open Spares Requests</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Request #</th>
                            <th>Enduser</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($openSparesRequests as $sr)
                            <tr>
                                <td>
                                    <a href="{{ route('sorders.store_list_view', $sr->id) }}">
                                        {{ $sr->request_number ?? 'SR-'.$sr->id }}
                                    </a>
                                </td>
                                <td>{{ optional($sr->enduser)->asset_staff_id ?? 'N/A' }}</td>
                                <td>{{ $sr->status ?? 'N/A' }}</td>
                                <td>{{ optional($sr->created_at)->format('d-M-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No open spares requests.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Technical Sidebar / Quick Actions -->
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
                    <a href="#" class="btn btn-secondary mr-2" disabled>
                        [+] Log Daily Usage (Coming Soon)
                    </a>
                    <a href="{{ route('inventory-corrections.index') }}" class="btn btn-primary mr-2">
                        [+] Approve Inventory Adjustment
                    </a>
                    <a href="{{ route('monthlyreport') }}" class="btn btn-outline-dark">
                        [Export Monthly TCO Report]
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

