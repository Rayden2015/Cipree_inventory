@extends('layouts.admin')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-upload"></i> Bulk Import Suppliers</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
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
                    <form action="{{ route('suppliers.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Instructions:</h5>
                                <ul>
                                    <li>Download the template file to see the required format</li>
                                    <li>Supported formats: CSV, XLSX, XLS (max 10MB)</li>
                                    <li>Required columns: name</li>
                                    <li>Optional columns: address, location, tel, phone, email, items_supplied, contact_person, primary_currency, comp_reg_no, vat_reg_no, item_cat1, item_cat2, item_cat3</li>
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
                                <a href="{{ route('suppliers.import.template') }}" class="btn btn-info">
                                    <i class="fas fa-download mr-2"></i>Download Template
                                </a>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload mr-2"></i>Import Suppliers
                            </button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>

                @if (session('import_stats'))
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Import Statistics</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Successfully Imported</span>
                                            <span class="info-box-number">{{ session('import_stats')['success'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Failed</span>
                                            <span class="info-box-number">{{ session('import_stats')['failed'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('import_failures') && count(session('import_failures')) > 0)
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Import Errors</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Row</th>
                                            <th>Column</th>
                                            <th>Error</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (session('import_failures') as $failure)
                                            <tr>
                                                <td>{{ $failure->row() }}</td>
                                                <td>{{ implode(', ', $failure->attribute()) }}</td>
                                                <td>{{ implode(', ', $failure->errors()) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Update file input label
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = e.target.files[0]?.name || 'Choose file';
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>
@endpush
