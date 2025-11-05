@extends('layouts.admin')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 9v4"></path>
                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                <path d="M12 16h.01"></path>
                            </svg>
                            Error Logs
                        </h3>
                        <div class="ms-auto">
                            <span class="badge bg-red text-white">{{ isset($errorLogs) ? $errorLogs->total() : 0 }} Total Errors</span>
                        </div>
                    </div>

                    @if(isset($table_missing) && $table_missing)
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong>Warning!</strong> The error_logs table does not exist yet. Run the migration to create it:
                            <br><code>php artisan migrate --path=database/migrations/2024_07_07_073419_create_error_logs_table.php</code>
                        </div>
                    </div>
                    @else

                    <!-- Search Form -->
                    <div class="card-body border-bottom">
                        <form action="{{ route('error-logs.search') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Error ID</label>
                                    <input type="text" name="error_id" class="form-control" placeholder="762253241" value="{{ request('error_id') }}">
                                    <small class="text-muted">9-digit error ID shown to users</small>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Controller/Message</label>
                                    <input type="text" name="controller" class="form-control" placeholder="UserController" value="{{ request('controller') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                            Search
                                        </button>
                                        <a href="{{ route('error-logs.index') }}" class="btn btn-link">Clear</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Legacy File Search -->
                    <div class="card-body border-bottom bg-light">
                        <div class="row align-items-center">
                            <div class="col">
                                <strong>Search Legacy Log Files</strong>
                                <small class="text-muted d-block">For errors before database logging was enabled</small>
                            </div>
                            <div class="col-auto">
                                <form action="{{ route('error-logs.search-files') }}" method="GET" class="d-flex gap-2">
                                    <input type="text" name="error_id" class="form-control" placeholder="Error ID" required style="width: 200px;">
                                    <button type="submit" class="btn btn-secondary">Search Files</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Error Logs Table -->
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date/Time</th>
                                    <th>Level</th>
                                    <th>Message</th>
                                    <th>IP Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($errorLogs as $log)
                                <tr>
                                    <td class="text-muted">{{ $log->id }}</td>
                                    <td>
                                        <span class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d-M-Y') }}</span><br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($log->level_name == 'ERROR')
                                            <span class="badge bg-red">{{ $log->level_name }}</span>
                                        @elseif($log->level_name == 'WARNING')
                                            <span class="badge bg-yellow">{{ $log->level_name }}</span>
                                        @else
                                            <span class="badge bg-blue">{{ $log->level_name }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 500px;">
                                            {{ $log->message }}
                                        </div>
                                        @if(preg_match('/ERROR_ID:(\d+)/', $log->message, $matches))
                                            <span class="badge bg-orange mt-1">Error ID: {{ $matches[1] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->remote_addr)
                                            <code>{{ $log->remote_addr }}</code>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('error-logs.show', $log->id) }}" class="btn btn-sm btn-primary">
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 8a3.5 3 0 0 1 3.5 -3h1a3.5 3 0 0 1 3.5 3a3 3 0 0 1 -2 3a3 4 0 0 0 -2 4" /><path d="M12 19l0 .01" /></svg>
                                        <p>No error logs found.</p>
                                        @if(request()->hasAny(['error_id', 'controller', 'date_from', 'date_to']))
                                            <a href="{{ route('error-logs.index') }}" class="btn btn-primary btn-sm">Clear Search</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($errorLogs) && $errorLogs->hasPages())
                    <div class="card-footer">
                        {{ $errorLogs->links() }}
                    </div>
                    @endif

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

