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
                               placeholder="Search by Request # or Work Order #">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Search</button>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-default mb-2 ml-2">Reset</a>
                </form>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Request Number</th>
                        <th>Work Order Number</th>
                        <th>Status</th>
                        <th>Requested By</th>
                        <th>Enduser</th>
                        <th>Requested On</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($work_orders as $index => $wo)
                        <tr>
                            <td>{{ $work_orders->firstItem() + $index }}</td>
                            <td>{{ $wo->request_number }}</td>
                            <td>{{ $wo->work_order_number ?? 'N/A' }}</td>
                            <td>{{ $wo->status }}</td>
                            <td>{{ optional($wo->request_by)->name ?? 'N/A' }}</td>
                            <td>{{ optional($wo->enduser)->name_description ?? 'N/A' }}</td>
                            <td>{{ optional($wo->created_at)->format('d-M-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No Work Orders found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $work_orders->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</section>
@endsection