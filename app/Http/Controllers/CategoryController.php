<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-item-group'])->only('show');
        $this->middleware(['auth', 'permission:add-item-group'])->only('create');
        $this->middleware(['auth', 'permission:view-item-group'])->only('index');
        $this->middleware(['auth', 'permission:edit-item-group'])->only('edit');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Category::latest();

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $categories = $query->paginate(20)->withQueryString();

            Log::info('CategoryController | index() | Categories loaded successfully', [
                'user_details' => Auth::user(),
                'count' => $categories->total(),
                'current_page' => $categories->currentPage(),
            ]);
            return view('categories.index', compact('categories'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('CategoryController | Index() Error ' . $unique_id, [
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
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $user = Auth::user();
            $site_id = $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;
            
            Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'site_id' => $site_id,
                'tenant_id' => $tenant_id,
            ]);
            
            Log::info(
                'CategoryController | store() | added a new category',
                [
                    'user_details' => Auth::user(),
                    'category_name' => $request->name,
                    'description' => $request->description,
                ]
            );

            return redirect()->route('categories.index')->with('success', 'Category created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('CategoryController | Store() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::find($id);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            $category = Category::findOrFail($id);
            Log::info('Category Edit', [
                'before_category_edit' => $category->toArray(),
            ]);

            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            Log::info(
                'CategoryController | update() | edited a category',
                [
                    'user_details' => Auth::user(),
                    'category_id' => $id,
                    'category_name' => $request->name,
                    'description' => $request->description,
                ]
            );

            return redirect()->route('categories.index')->with('success', 'Category updated successfully');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('CategoryController | Update() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $categoryName = $category->name;
            $category->delete();
            
            Log::info(
                'CategoryController | destroy() | Deleted a category',
                [
                    'user_details' => Auth::user(),
                    'category_name' => $categoryName,
                ]
            );

            return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('CategoryController | Destroy() Error ' . $unique_id, [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
        }
    }
}
