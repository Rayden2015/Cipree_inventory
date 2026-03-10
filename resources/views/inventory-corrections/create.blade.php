@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <a href="{{ route('inventories.edit', $item->inventory_id) }}" class="btn btn-outline-secondary">← Back to GRN</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5>Flag Error for Correction</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">You are requesting a correction for this line. A Supervisor must approve before the system applies the change (Void + Re-issue).</p>
            <table class="table table-bordered mb-4">
                <tr><th>Line ID</th><td>{{ $item->id }}</td></tr>
                <tr><th>Item</th><td>{{ optional($item->item)->item_description ?? $item->description }} ({{ optional($item->item)->item_stock_code ?? $item->stock_code }})</td></tr>
                <tr><th>Current quantity</th><td>{{ $item->quantity }}</td></tr>
                <tr><th>Current unit cost (GHS)</th><td>{{ $item->unit_cost_exc_vat_gh }}</td></tr>
                <tr><th>GRN</th><td>{{ optional($item->inventory)->grn_number }}</td></tr>
            </table>

            <form action="{{ route('inventory-corrections.store') }}" method="POST">
                @csrf
                <input type="hidden" name="inventory_item_id" value="{{ $item->id }}">
                <div class="form-group">
                    <label>Intended quantity <span class="text-danger">*</span></label>
                    <input type="number" name="intended_quantity" class="form-control" value="{{ old('intended_quantity', $item->quantity) }}" min="0" step="1" required>
                    @error('intended_quantity')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Intended unit cost (GHS) — leave blank to keep current</label>
                    <input type="number" name="intended_unit_cost_exc_vat_gh" class="form-control" value="{{ old('intended_unit_cost_exc_vat_gh', $item->unit_cost_exc_vat_gh) }}" min="0" step="0.01">
                    @error('intended_unit_cost_exc_vat_gh')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit for Approval</button>
                <a href="{{ route('inventories.edit', $item->inventory_id) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
