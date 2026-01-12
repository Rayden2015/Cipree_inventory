<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Site;
use App\Models\Location;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PartsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Part::with(['supplier', 'site', 'location'])->latest();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $parts = $query->paginate(20)->withQueryString();

            Log::info('PartsController | index() | Parts loaded successfully', [
                'user_details' => Auth::user(),
                'count' => $parts->total(),
                'current_page' => $parts->currentPage(),
            ]);

            return view('parts.index', compact('parts'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Index() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $user = Auth::user();
            $site_id = $user->site->id;
            
            $suppliers = Supplier::where('site_id', $site_id)->get();
            $sites = Site::where('tenant_id', $user->getCurrentTenant()?->id)->get();
            $locations = Location::where('site_id', $site_id)->get();

            return view('parts.create', compact('suppliers', 'sites', 'locations'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Create() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'site_id' => 'required|exists:sites,id',
                'location_id' => 'nullable|exists:locations,id',
                'quantity' => 'nullable|integer|min:0'
            ]);

            $user = Auth::user();
            $site_id = $request->site_id ?? $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;

            Part::create([
                'name' => $request->name,
                'description' => $request->description,
                'supplier_id' => $request->supplier_id,
                'site_id' => $site_id,
                'location_id' => $request->location_id,
                'quantity' => $request->quantity ?? 0,
                'tenant_id' => $tenant_id,
            ]);

            Log::info(
                'PartsController | store() | added a new part',
                [
                    'user_details' => Auth::user(),
                    'part_name' => $request->name,
                ]
            );

            return redirect()->route('parts.index')->with('success', 'Part created successfully');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Store() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $part = Part::with(['supplier', 'site', 'location'])->findOrFail($id);
            return view('parts.show', compact('part'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Show() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            Toastr::error('Part not found.');
            return redirect()->route('parts.index');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $part = Part::findOrFail($id);
            $user = Auth::user();
            $site_id = $user->site->id;
            
            $suppliers = Supplier::where('site_id', $site_id)->get();
            $sites = Site::where('tenant_id', $user->getCurrentTenant()?->id)->get();
            $locations = Location::where('site_id', $site_id)->get();

            return view('parts.edit', compact('part', 'suppliers', 'sites', 'locations'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Edit() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            Toastr::error('Part not found.');
            return redirect()->route('parts.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'supplier_id' => 'nullable|exists:suppliers,id',
                'site_id' => 'required|exists:sites,id',
                'location_id' => 'nullable|exists:locations,id',
                'quantity' => 'nullable|integer|min:0'
            ]);

            $part = Part::findOrFail($id);
            $user = Auth::user();
            $site_id = $request->site_id ?? $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;

            Log::info('Part Edit', [
                'before_part_edit' => $part->toArray(),
            ]);

            $part->name = $request->name;
            $part->description = $request->description;
            $part->supplier_id = $request->supplier_id;
            $part->site_id = $site_id;
            $part->location_id = $request->location_id;
            $part->quantity = $request->quantity ?? 0;
            $part->tenant_id = $tenant_id;
            $part->save();

            Log::info(
                'PartsController | update() | edited a part',
                [
                    'user_details' => Auth::user(),
                    'part_id' => $id,
                    'part_name' => $request->name,
                ]
            );

            return redirect()->route('parts.index')->with('success', 'Part updated successfully');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Update() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $part = Part::findOrFail($id);
            $partName = $part->name;
            $part->delete();

            Log::info(
                'PartsController | destroy() | Deleted a part',
                [
                    'user_details' => Auth::user(),
                    'part_name' => $partName,
                ]
            );

            return redirect()->route('parts.index')->with('success', 'Part deleted successfully');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | Destroy() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * AJAX search for suppliers (used in select2 dropdowns)
     */
    public function selectSearch(Request $request)
    {
        try {
            $user = Auth::user();
            $site_id = $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id;

            $suppliers = Supplier::where('site_id', $site_id);
            
            if ($tenant_id) {
                $suppliers->where('tenant_id', $tenant_id);
            }

            if ($request->has('q')) {
                $search = $request->q;
                $suppliers->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('phone', 'LIKE', "%$search%");
                });
            }

            $suppliers = $suppliers->select("id", "name")->get();

            Log::info('PartsController | selectSearch', [
                'user_details' => auth()->user(),
                'message' => 'Supplier search completed successfully.',
            ]);

            return response()->json($suppliers);
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | SelectSearch() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * AJAX search for sites (used in select2 dropdowns)
     */
    public function selectSite(Request $request)
    {
        try {
            $user = Auth::user();
            $tenant_id = $user->getCurrentTenant()?->id;

            $sites = Site::where('tenant_id', $tenant_id);

            if ($request->has('q')) {
                $search = $request->q;
                $sites->where('name', 'LIKE', "%$search%");
            }

            $sites = $sites->select("id", "name")->get();

            Log::info('PartsController | selectSite', [
                'user_details' => auth()->user(),
                'message' => 'Site search completed successfully.',
            ]);

            return response()->json($sites);
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | SelectSite() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Search failed'], 500);
        }
    }

    /**
     * AJAX search for locations (used in select2 dropdowns)
     */
    public function selectLocation(Request $request)
    {
        try {
            $user = Auth::user();
            $site_id = $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id;

            $locations = Location::where('site_id', $site_id);
            
            if ($tenant_id) {
                $locations->where('tenant_id', $tenant_id);
            }

            if ($request->has('q')) {
                $search = $request->q;
                $locations->where('name', 'LIKE', "%$search%");
            }

            $locations = $locations->select("id", "name")->get();

            Log::info('PartsController | selectLocation', [
                'user_details' => auth()->user(),
                'message' => 'Location search completed successfully.',
            ]);

            return response()->json($locations);
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('PartsController | SelectLocation() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'Search failed'], 500);
        }
    }
}
