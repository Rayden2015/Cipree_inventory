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
    public function index()
    {
        try {
            $site_id = Auth::user()->site->id;
            $categories = Category::latest()->paginate(20);
            Log::info('CategoryController | index() | Catories loaded successfully', [
                'user_details' => Auth::user(),
                'categories' => $categories
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
                'name' => 'nullable'
            ]);
            $site_id = Auth::user()->site->id;
            Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'site_id' => $site_id,
            ]);
            $authId = Auth::user()->name;
            Log::info(
                'CategoryController | store() | added a new category',
                [
                    'user_details' => Auth::user(),
                    'category_name' => $request->name,
                    'description' => $request->description,
                ]
            );

            return redirect()->back()->withSuccess('Successfully Updated');
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
            //code...
            $category = Category::find($id);
            Log::info('Category Edit', [
                'before_category_edit' => Category::find($id),
            ]);

            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            $authId = Auth::user()->name;
            Log::info(
                'edited a category',
                [
                    'user_name' => $authId,
                    'category_name' => $request->name,
                    'description' => $request->description,
                ]
            );

            return redirect()->back()->withSuccess('Successfully Updated');
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
            $category = Category::find($id);
            $authId = Auth::user()->name;
            $category->delete();
            Log::info(
                'CategoryController| destory()| Deleted a category',
                [
                    'user_name' => Auth::user(),
                    'category_name' => $category->name,
                ]
            );

            return redirect()->back()->withSuccess('Successfully Updated');
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
