<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ErrorLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only allow admins to view error logs
        $this->middleware('permission:view-error-logs|Super Admin|Site Admin')->only(['index', 'show', 'search']);
    }

    /**
     * Display error logs
     */
    public function index()
    {
        try {
            // Check if error_logs table exists
            if (!DB::getSchemaBuilder()->hasTable('error_logs')) {
                return view('errors.logs.index', [
                    'errorLogs' => collect([]),
                    'table_missing' => true
                ]);
            }

            $errorLogs = DB::table('error_logs')
                ->orderBy('id', 'desc')
                ->paginate(50);

            return view('errors.logs.index', compact('errorLogs'));
        } catch (\Exception $e) {
            Log::error('ErrorLogController | index() | Error loading error logs', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withError('Unable to load error logs. Please contact the system administrator.');
        }
    }

    /**
     * Search error logs by error ID, user, or message
     */
    public function search(Request $request)
    {
        try {
            $query = DB::table('error_logs')
                ->orderBy('id', 'desc');

            // Search by error ID (extracted from message)
            if ($request->filled('error_id')) {
                $errorId = $request->input('error_id');
                $query->where('message', 'like', '%ERROR_ID:' . $errorId . '%')
                      ->orWhere('message', 'like', '%Error ' . $errorId . '%')
                      ->orWhere('message', 'like', '%with id ' . $errorId . '%');
            }

            // Search by user email
            if ($request->filled('user_email')) {
                $query->where('context', 'like', '%' . $request->input('user_email') . '%');
            }

            // Search by date range
            if ($request->filled('date_from')) {
                $query->where('created_at', '>=', $request->input('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->where('created_at', '<=', $request->input('date_to') . ' 23:59:59');
            }

            // Search by controller/message
            if ($request->filled('controller')) {
                $query->where('message', 'like', '%' . $request->input('controller') . '%');
            }

            // Search by error level
            if ($request->filled('level')) {
                $query->where('level_name', $request->input('level'));
            }

            $errorLogs = $query->paginate(50)->appends($request->all());

            return view('errors.logs.index', compact('errorLogs'));
        } catch (\Exception $e) {
            Log::error('ErrorLogController | search() | Error searching logs', [
                'error' => $e->getMessage(),
                'search_params' => $request->all(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('error-logs.index')
                ->withError('Error searching logs. Please try again.');
        }
    }

    /**
     * Show detailed error log
     */
    public function show($id)
    {
        try {
            $errorLog = DB::table('error_logs')->find($id);

            if (!$errorLog) {
                return redirect()->route('error-logs.index')
                    ->withError('Error log not found.');
            }

            // Parse context JSON
            $context = json_decode($errorLog->context, true) ?? [];
            $extra = json_decode($errorLog->extra, true) ?? [];

            return view('errors.logs.show', compact('errorLog', 'context', 'extra'));
        } catch (\Exception $e) {
            Log::error('ErrorLogController | show() | Error viewing error log', [
                'error' => $e->getMessage(),
                'log_id' => $id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('error-logs.index')
                ->withError('Unable to view error log details.');
        }
    }

    /**
     * Search from file logs (legacy support)
     */
    public function searchFiles(Request $request)
    {
        try {
            $errorId = $request->input('error_id');
            
            if (empty($errorId)) {
                return redirect()->back()
                    ->withError('Please provide an error ID to search.');
            }

            $logFile = storage_path('logs/errors/error.log');
            
            if (!file_exists($logFile)) {
                return redirect()->back()
                    ->withError('Error log file not found.');
            }

            // Search for the error ID in the log file
            $command = "grep -A 50 " . escapeshellarg($errorId) . " " . escapeshellarg($logFile);
            $output = shell_exec($command);

            if (empty($output)) {
                return view('errors.logs.search-result', [
                    'errorId' => $errorId,
                    'found' => false
                ]);
            }

            return view('errors.logs.search-result', [
                'errorId' => $errorId,
                'found' => true,
                'output' => $output
            ]);
        } catch (\Exception $e) {
            Log::error('ErrorLogController | searchFiles() | Error searching file logs', [
                'error' => $e->getMessage(),
                'error_id' => $request->input('error_id'),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withError('Error searching log files.');
        }
    }
}

