@extends('layouts.admin')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Error Log Details - ID #{{ $errorLog->id }}</h3>
                        <div class="ms-auto">
                            <a href="{{ route('error-logs.index') }}" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                                Back to List
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Error Summary -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 150px;">Error ID:</th>
                                        <td>
                                            @if(preg_match('/ERROR_ID:(\d+)/', $errorLog->message, $matches))
                                                <span class="badge bg-orange fs-4">{{ $matches[1] }}</span>
                                            @else
                                                <span class="text-muted">Not available</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Date/Time:</th>
                                        <td>{{ \Carbon\Carbon::parse($errorLog->created_at)->format('d-M-Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Level:</th>
                                        <td>
                                            <span class="badge bg-red">{{ $errorLog->level_name }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Channel:</th>
                                        <td><code>{{ $errorLog->channel }}</code></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th style="width: 150px;">User:</th>
                                        <td>
                                            @if(isset($context['user_name']))
                                                {{ $context['user_name'] }}
                                                @if(isset($context['user_email']))
                                                    <br><small class="text-muted">{{ $context['user_email'] }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Guest</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>IP Address:</th>
                                        <td>
                                            @if($errorLog->remote_addr)
                                                <code>{{ $errorLog->remote_addr }}</code>
                                            @elseif(isset($context['ip_address']))
                                                <code>{{ $context['ip_address'] }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Controller:</th>
                                        <td>
                                            @if(isset($context['controller']))
                                                <code>{{ $context['controller'] }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Method:</th>
                                        <td>
                                            @if(isset($context['method']))
                                                <code>{{ $context['method'] }}</code>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div class="mb-3">
                            <strong>Error Message:</strong>
                            <div class="alert alert-danger mt-2">
                                {{ $errorLog->message }}
                            </div>
                        </div>

                        <!-- Error Details -->
                        @if(isset($context['error_message']))
                        <div class="mb-3">
                            <strong>Detailed Error Message:</strong>
                            <pre class="bg-light p-3 rounded"><code>{{ $context['error_message'] }}</code></pre>
                        </div>
                        @endif

                        <!-- Error Location -->
                        @if(isset($context['error_file']) && isset($context['error_line']))
                        <div class="mb-3">
                            <strong>Error Location:</strong>
                            <div class="bg-light p-3 rounded">
                                <strong>File:</strong> <code>{{ $context['error_file'] }}</code><br>
                                <strong>Line:</strong> <code>{{ $context['error_line'] }}</code>
                            </div>
                        </div>
                        @endif

                        <!-- URL -->
                        @if(isset($context['url']))
                        <div class="mb-3">
                            <strong>Request URL:</strong>
                            <div class="bg-light p-3 rounded">
                                <code>{{ $context['http_method'] ?? 'GET' }}</code> <code>{{ $context['url'] }}</code>
                            </div>
                        </div>
                        @endif

                        <!-- Request Data -->
                        @if(isset($context['request_data']) && !empty($context['request_data']))
                        <div class="mb-3">
                            <strong>Request Data:</strong>
                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($context['request_data'], JSON_PRETTY_PRINT) }}</code></pre>
                        </div>
                        @endif

                        <!-- Stack Trace -->
                        @if(isset($context['stack_trace']))
                        <div class="mb-3">
                            <strong>Stack Trace:</strong>
                            <details>
                                <summary class="btn btn-sm btn-outline-secondary mb-2">Show/Hide Stack Trace</summary>
                                <pre class="bg-dark text-white p-3 rounded" style="font-size: 12px; max-height: 400px; overflow-y: auto;"><code>{{ $context['stack_trace'] }}</code></pre>
                            </details>
                        </div>
                        @endif

                        <!-- User Agent -->
                        @if($errorLog->user_agent || isset($context['user_agent']))
                        <div class="mb-3">
                            <strong>User Agent:</strong>
                            <pre class="bg-light p-2 rounded"><code>{{ $errorLog->user_agent ?? $context['user_agent'] }}</code></pre>
                        </div>
                        @endif

                        <!-- Full Context -->
                        <div class="mb-3">
                            <strong>Full Context (JSON):</strong>
                            <details>
                                <summary class="btn btn-sm btn-outline-secondary mb-2">Show/Hide Full Context</summary>
                                <pre class="bg-light p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{{ json_encode($context, JSON_PRETTY_PRINT) }}</code></pre>
                            </details>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

