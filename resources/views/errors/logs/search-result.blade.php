@extends('layouts.admin')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">File Search Results for Error ID: {{ $errorId }}</h3>
                        <div class="ms-auto">
                            <a href="{{ route('error-logs.index') }}" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                                Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($found)
                            <div class="alert alert-success">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>
                                <strong>Found!</strong> Error ID {{ $errorId }} found in log files.
                            </div>

                            <strong>Log Output:</strong>
                            <pre class="bg-dark text-white p-3 rounded mt-2" style="max-height: 600px; overflow-y: auto; font-size: 12px;"><code>{{ $output }}</code></pre>
                        @else
                            <div class="alert alert-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
                                <strong>Not Found!</strong> Error ID {{ $errorId }} was not found in log files.
                            </div>

                            <p class="text-muted">
                                This error ID may not exist, or the log files may have been rotated/deleted. 
                                Try searching in the database logs instead.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

