@extends('layouts.admin')

@section('content')
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            $('#asset_enduser_id').select2({
                width: '100%',
                placeholder: '-- Select Asset (optional) --'
            });
            $('#responsible_enduser_id').select2({
                width: '100%',
                placeholder: '-- Select Responsible Person --'
            });
        });
    </script>
@endsection
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Work Order</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">New Work Order</h3>
            </div>
            <form method="POST" action="{{ route('work-orders.store') }}">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title"
                               class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" required>
                        @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="4">{{ old('description') }}</textarea>
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
                                    <option value="{{ $priority }}" {{ old('priority','Medium') === $priority ? 'selected' : '' }}>
                                        {{ $priority }}
                                    </option>
                                @endforeach
                            </select>
                            @error('priority')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="requested_date">Requested Date</label>
                            <input type="datetime-local" name="requested_date" id="requested_date"
                                   class="form-control @error('requested_date') is-invalid @enderror"
                                   value="{{ old('requested_date') }}">
                            @error('requested_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="due_date">Due Date</label>
                            <input type="datetime-local" name="due_date" id="due_date"
                                   class="form-control @error('due_date') is-invalid @enderror"
                                   value="{{ old('due_date') }}">
                            @error('due_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="asset_state">Asset State</label>
                            <select name="asset_state" id="asset_state"
                                    class="form-control @error('asset_state') is-invalid @enderror">
                                @foreach(['Operational','Down','Standby'] as $state)
                                    <option value="{{ $state }}" {{ old('asset_state','Operational') === $state ? 'selected' : '' }}>
                                        {{ $state }}
                                    </option>
                                @endforeach
                            </select>
                            @error('asset_state')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="asset_down_since">Asset Went Down At</label>
                            <input type="datetime-local" name="asset_down_since" id="asset_down_since"
                                   class="form-control @error('asset_down_since') is-invalid @enderror"
                                   value="{{ old('asset_down_since') }}">
                            <small class="form-text text-muted">
                                Required when Asset State is set to Down (used for downtime).
                            </small>
                            @error('asset_down_since')
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
                                    <option value="{{ $asset->id }}" {{ old('asset_enduser_id') == $asset->id ? 'selected' : '' }}>
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
                                    <option value="{{ $person->id }}" {{ old('responsible_enduser_id') == $person->id ? 'selected' : '' }}>
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
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection

