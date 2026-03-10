@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <a href="{{ route('inventory-corrections.index') }}" class="btn btn-outline-secondary">← Back to Pending</a>
        </div>
    </div>

    @if ($shortfall < 0)
        <div class="alert alert-warning">
            <strong>Ghost stock alert:</strong> Stores Officer is correcting to <strong>{{ $correctionRequest->intended_quantity }}</strong> units, but <strong>{{ $issuedQty }}</strong> have already been issued. This creates a shortfall of <strong>{{ $shortfall }}</strong> units. You may still approve; the system will create a negative adjustment and the net balance will require reconciliation.
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header"><h5>Correction Request #{{ $correctionRequest->id }}</h5></div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>GRN</th><td>{{ optional($correctionRequest->inventoryItem->inventory)->grn_number }}</td></tr>
                <tr><th>Item</th><td>{{ optional($correctionRequest->inventoryItem->item)->item_description }} ({{ optional($correctionRequest->inventoryItem->item)->item_stock_code }})</td></tr>
                <tr><th>Current quantity</th><td>{{ $correctionRequest->inventoryItem->quantity }}</td></tr>
                <tr><th>Intended quantity</th><td>{{ $correctionRequest->intended_quantity }}</td></tr>
                <tr><th>Intended unit cost (GHS)</th><td>{{ $correctionRequest->intended_unit_cost_exc_vat_gh ?? $correctionRequest->inventoryItem->unit_cost_exc_vat_gh }}</td></tr>
                <tr><th>Already issued</th><td>{{ $issuedQty }}</td></tr>
                <tr><th>Requested by</th><td>{{ optional($correctionRequest->requestedByUser)->name }} on {{ $correctionRequest->created_at->format('d M Y H:i') }}</td></tr>
                @if($correctionRequest->notes)<tr><th>Notes</th><td>{{ $correctionRequest->notes }}</td></tr>@endif
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Approve (Supervisor)</h5></div>
        <div class="card-body">
            <form action="{{ route('inventory-corrections.approve', $correctionRequest->id) }}" method="POST" class="mb-4">
                @csrf
                <div class="form-group">
                    <label>Reason code <span class="text-danger">*</span></label>
                    <select name="reason_code_id" class="form-control" required>
                        <option value="">Select reason...</option>
                        @foreach($reasonCodes as $rc)
                            <option value="{{ $rc->id }}" {{ old('reason_code_id') == $rc->id ? 'selected' : '' }}>{{ $rc->code }} — {{ $rc->type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">Approve & Apply Correction</button>
            </form>

            <hr>
            <form action="{{ route('inventory-corrections.reject', $correctionRequest->id) }}" method="POST" onsubmit="return confirm('Reject this correction request?');">
                @csrf
                <div class="form-group">
                    <label>Rejection reason (optional)</label>
                    <input type="text" name="rejection_reason" class="form-control" placeholder="Reason for rejection">
                </div>
                <button type="submit" class="btn btn-danger">Reject Request</button>
            </form>
        </div>
    </div>
</div>
@endsection
