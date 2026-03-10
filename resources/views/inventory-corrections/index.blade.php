@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Pending Correction Requests</h4>
            <p class="text-muted">Approve or reject requests from Stores Officers to correct GRN entries.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory-corrections.audit-log') }}" class="btn btn-outline-secondary">Audit Log (All Statuses)</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>GRN / Item</th>
                        <th>Current Qty</th>
                        <th>Intended Qty</th>
                        <th>Requested By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td>{{ $req->id }}</td>
                            <td>
                                GRN {{ optional($req->inventoryItem->inventory)->grn_number ?? 'N/A' }} —
                                {{ optional($req->inventoryItem->item)->item_description ?? $req->inventoryItem->description ?? 'Item #'.$req->inventory_item_id }}
                            </td>
                            <td>{{ $req->inventoryItem->quantity ?? 0 }}</td>
                            <td>{{ $req->intended_quantity }}</td>
                            <td>{{ optional($req->requestedByUser)->name ?? 'N/A' }}</td>
                            <td>{{ $req->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('inventory-corrections.show', $req->id) }}" class="btn btn-sm btn-primary">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">No pending correction requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="card-footer">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
@endsection
