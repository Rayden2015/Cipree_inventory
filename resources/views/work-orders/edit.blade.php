@extends('layouts.admin')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Work Order {{ $workOrder->work_order_number }}</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Edit Work Order</h3>
            </div>
            <form method="POST" action="{{ route('work-orders.update', $workOrder) }}">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label>Work Order Number</label>
                        <input type="text" class="form-control" value="{{ $workOrder->work_order_number }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $workOrder->title) }}" required>
                        @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="4">{{ old('description', $workOrder->description) }}</textarea>
                        @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="priority">Priority</label>
                            <select name="priority" id="priority"
                                    class="form-control @error('priority') is-invalid @enderror">
                                @foreach(['Low','Medium','High','Critical'] as $priority)
                                    <option value="{{ $priority }}" {{ old('priority', $workOrder->priority) === $priority ? 'selected' : '' }}>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="status">Status</label>
                            <select name="status" id="status"
                                    class="form-control @error('status') is-invalid @enderror">
                                @foreach(['Open','In Progress','Completed','Cancelled'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $workOrder->status) === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="requested_date">Requested Date</label>
                            <input type="datetime-local" name="requested_date" id="requested_date"
                                   class="form-control @error('requested_date') is-invalid @enderror"
                                   value="{{ old('requested_date', optional($workOrder->requested_date)->format('Y-m-d\TH:i')) }}">
                            @error('requested_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="due_date">Due Date</label>
                            <input type="datetime-local" name="due_date" id="due_date"
                                   class="form-control @error('due_date') is-invalid @enderror"
                                   value="{{ old('due_date', optional($workOrder->due_date)->format('Y-m-d\TH:i')) }}">
                            @error('due_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="completed_date">Completed Date</label>
                            <input type="datetime-local" name="completed_date" id="completed_date"
                                   class="form-control @error('completed_date') is-invalid @enderror"
                                   value="{{ old('completed_date', optional($workOrder->completed_date)->format('Y-m-d\TH:i')) }}">
                            @error('completed_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="asset_enduser_id">Asset (Equipment / Machine)</label>
                            <select name="asset_enduser_id" id="asset_enduser_id"
                                    class="form-control @error('asset_enduser_id') is-invalid @enderror">
                                <option value="">-- Select Asset (optional) --</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" {{ old('asset_enduser_id', $workOrder->asset_enduser_id) == $asset->id ? 'selected' : '' }}>
                                        {{ $asset->name_description ?? $asset->name }} ({{ $asset->asset_staff_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_enduser_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-6">
                            <label for="responsible_enduser_id">Responsible Person</label>
                            <select name="responsible_enduser_id" id="responsible_enduser_id"
                                    class="form-control @error('responsible_enduser_id') is-invalid @enderror" required>
                                <option value="">-- Select Responsible Person --</option>
                                @foreach($people as $person)
                                    <option value="{{ $person->id }}" {{ old('responsible_enduser_id', $workOrder->responsible_enduser_id) == $person->id ? 'selected' : '' }}>
                                        {{ $person->name_description ?? $person->name }} ({{ $person->asset_staff_id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('responsible_enduser_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

