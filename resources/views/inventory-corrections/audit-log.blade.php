@extends('layouts.admin')
@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4>Inventory Ledger (Audit)</h4>
            <p class="text-muted">Full forensic view: Active, Voided, and Adjustment entries.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('inventory-corrections.index') }}" class="btn btn-outline-secondary">Pending Corrections</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>GRN</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Unit cost</th>
                        <th>Source line</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lines as $line)
                        <tr>
                            <td>{{ $line->id }}</td>
                            <td><span class="badge badge-{{ $line->status === 'Active' ? 'success' : ($line->status === 'Voided' ? 'secondary' : 'info') }}">{{ $line->status }}</span></td>
                            <td>{{ optional($line->inventory)->grn_number }}</td>
                            <td>{{ optional($line->item)->item_description ?? $line->description }} ({{ optional($line->item)->item_stock_code ?? $line->stock_code }})</td>
                            <td>{{ $line->quantity }}</td>
                            <td>{{ $line->unit_cost_exc_vat_gh }}</td>
                            <td>{{ $line->source_inventory_item_id ? '#' . $line->source_inventory_item_id : '—' }}</td>
                            <td>{{ $line->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">No ledger entries.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lines->hasPages())
            <div class="card-footer">{{ $lines->links() }}</div>
        @endif
    </div>
</div>
@endsection
