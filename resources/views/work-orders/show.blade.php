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
                        @if($workOrder->asset)
                            {{ $workOrder->asset->name_description ?? $workOrder->asset->name }}
                            ({{ $workOrder->asset->asset_staff_id }})
                            @if($workOrder->asset->type)
                                - {{ $workOrder->asset->type }}
                            @endif
                        @else
                            N/A
                        @endif
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

                    <dt class="col-sm-3">Asset State</dt>
                    <dd class="col-sm-9">
                        {{ $workOrder->asset_state ?? 'Operational' }}
                    </dd>

                    <dt class="col-sm-3">Asset Went Down At</dt>
                    <dd class="col-sm-9">
                        {{ optional($workOrder->asset_down_since)->format('d-M-Y H:i') ?? 'N/A' }}
                    </dd>

                    <dt class="col-sm-3">Work Done Details</dt>
                    <dd class="col-sm-9">
                        {{ $workOrder->work_done_details ?? 'N/A' }}
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

                <hr>
                <div class="mt-3">
                    <a href="{{ route('stores.request_search', ['work_order_number' => $workOrder->work_order_number]) }}"
                       class="btn btn-primary">
                        Create Parts Request for this Work Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

