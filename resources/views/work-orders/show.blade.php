@extends('layouts.admin')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Work Order {{ $workOrder->work_order_number }}</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Details</h3>
                <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-light btn-sm">Edit</a>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Work Order #</dt>
                    <dd class="col-sm-9">{{ $workOrder->work_order_number }}</dd>

                    <dt class="col-sm-3">Title</dt>
                    <dd class="col-sm-9">{{ $workOrder->title }}</dd>

                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{{ $workOrder->description ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Status</dt>
                    <dd class="col-sm-9">{{ $workOrder->status }}</dd>

                    <dt class="col-sm-3">Priority</dt>
                    <dd class="col-sm-9">{{ $workOrder->priority }}</dd>

                    <dt class="col-sm-3">Asset</dt>
                    <dd class="col-sm-9">
                        {{ optional($workOrder->asset)->name_description ?? 'N/A' }}
                    </dd>

                    <dt class="col-sm-3">Responsible Person</dt>
                    <dd class="col-sm-9">
                        {{ optional($workOrder->responsiblePerson)->name_description ?? 'N/A' }}
                    </dd>

                    <dt class="col-sm-3">Requested On</dt>
                    <dd class="col-sm-9">
                        {{ optional($workOrder->requested_date)->format('d-M-Y H:i') ?? 'N/A' }}
                    </dd>

                    <dt class="col-sm-3">Due Date</dt>
                    <dd class="col-sm-9">
                        {{ optional($workOrder->due_date)->format('d-M-Y H:i') ?? 'N/A' }}
                    </dd>

                    <dt class="col-sm-3">Completed Date</dt>
                    <dd class="col-sm-9">
                        {{ optional($workOrder->completed_date)->format('d-M-Y H:i') ?? 'N/A' }}
                    </dd>

                    <dt class="col-sm-3">Linked Requests</dt>
                    <dd class="col-sm-9">
                        @php($requests = $workOrder->storeRequests)
                        @if($requests->isEmpty())
                            <span class="text-muted">No stock requests linked.</span>
                        @else
                            <ul class="mb-0">
                                @foreach($requests as $req)
                                    <li>
                                        <a href="{{ route('sorders.store_list_view', $req->id) }}">
                                            {{ $req->request_number ?? 'SR-'.$req->id }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</section>
@endsection

