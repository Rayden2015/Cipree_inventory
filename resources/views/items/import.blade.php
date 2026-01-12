@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-upload"></i> Bulk Import Items</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('items.index') }}">Items</a></li>
                    <li class="breadcrumb-item active">Bulk Import</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-upload mr-2"></i>Upload CSV/XLSX File</h3>
                    </div>
                    <form action="{{ route('items.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Instructions:</h5>
                                <ul>
                                    <li>Download the template file to see the required format</li>
                                    <li>Supported formats: CSV, XLSX, XLS (max 10MB)</li>
                                    <li>Required columns: item_description, category_name, uom_name, reorder_level</li>
                                    <li>Optional columns: item_part_number (must be unique if provided)</li>
                                </ul>
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="file">Select File</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="file" name="file" accept=".csv,.xlsx,.xls" required>
                                        <label class="custom-file-label" for="file">Choose file</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted">File must be CSV, XLSX, or XLS format (max 10MB)</small>
                            </div>

                            <div class="form-group">
                                <a href="{{ route('items.import.template') }}" class="btn btn-info">
                                    <i class="fas fa-download mr-2"></i>Download Template
                                </a>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload mr-2"></i>Import Items
                            </button>
                            <a href="{{ route('items.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>

                @if (session('import_stats'))
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-check-circle mr-2"></i>Import Results</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Successfully imported:</strong> {{ session('import_stats')['success'] }} item(s)</p>
                            @if (session('import_stats')['failed'] > 0)
                                <p class="text-warning"><strong>Failed to import:</strong> {{ session('import_stats')['failed'] }} item(s)</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
    // Update file input label with selected filename
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>
@endpush
@endsection
