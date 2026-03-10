@extends('layouts.admin')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Work Orders</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form method="GET" action="{{ route('work-orders.index') }}" class="form-inline">
                    <div class="form-group mb-2 mr-2">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                               placeholder="Search by Work Order # or Title">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-default mb-2 ml-2">Reset</a>
                    <a href="{{ route('work-orders.create') }}" class="btn btn-success mb-2 ml-2">Create Work Order</a>
                </form>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Work Order #</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Request #</th>
                        <th>Asset</th>
                        <th>Responsible</th>
                        <th>Requested On</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($workOrders as $index => $wo)
                        <tr>
                            <td>{{ $workOrders->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ route('work-orders.show', $wo) }}">{{ $wo->work_order_number }}</a>
                            </td>
                            <td>{{ $wo->title }}</td>
                            <td>{{ $wo->status }}</td>
                            <td>{{ $wo->priority }}</td>
                            <td>
                                @php($requests = $wo->storeRequests)
                                @if($requests->isEmpty())
                                    <span class="text-muted">—</span>
                                @else
                                    @foreach($requests as $rIndex => $req)
                                        <a href="{{ route('sorders.store_list_view', $req->id) }}">
                                            {{ $req->request_number ?? 'SR-'.$req->id }}
                                        </a>@if($rIndex < $requests->count() - 1), @endif
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ optional($wo->asset)->name_description ?? 'N/A' }}</td>
                            <td>{{ optional($wo->responsiblePerson)->name_description ?? 'N/A' }}</td>
                            <td>{{ optional($wo->requested_date)->format('d-M-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No Work Orders found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $workOrders->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</section>
@endsection

