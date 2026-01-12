<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SuppliersImport;
use App\Exports\SuppliersImportTemplateExport;

class SupplierController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-supplier'])->only('show');
        $this->middleware(['auth', 'permission:add-supplier'])->only('create');
        $this->middleware(['auth', 'permission:view-supplier'])->only('index');
        $this->middleware(['auth', 'permission:edit-supplier'])->only('edit');
    }
    
    public function index()
    {
        try {
            $site_id = Auth::user()->site->id;
            $suppliers = Supplier::latest()->paginate(15);
            Log::info('SupplierController | index() | ', [
                'user_details' => Auth::user(),
                'message' => 'Suppliers loaded successfully',
                'response_payload' => $suppliers
            ]);
            return view('suppliers.index', compact('suppliers'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function create()
    {
        try {
            Log::info('SupplierController | create() | ', [
                'user_details' => Auth::user(),
                'message' => 'Suppliers Create Page Loaded successfully',
            ]);
            return view('suppliers.create');
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'string',
                'address' => 'string|nullable',
                'location' => 'string|nullable',
                'tel' => 'string|nullable',
                'phone' => 'string|nullable',
                'email' => 'string|nullable',
            ]);
            $user = Auth::user();
            $site_id = $user->site->id;
            $tenant_id = $user->getCurrentTenant()?->id ?? $user->site->tenant_id ?? null;
            Supplier::create([
                'name' => $request->name,
                'address' => $request->address,
                'location' => $request->location,
                'tel' => $request->tel,
                'phone' => $request->phone,
                'email' => $request->email,
                'items_supplied' => $request->items_supplied,
                'contact_person' => $request->contact_person,
                'primary_currency' => $request->primary_currency,
                'comp_reg_no' => $request->comp_reg_no,
                'vat_reg_no' => $request->vat_reg_no,
                'item_cat1' => $request->item_cat1,
                'item_cat2' => $request->item_cat2,
                'item_cat3' => $request->item_cat3,
                'site_id' => $site_id,
                'tenant_id' => $tenant_id,
            ]);

            Log::info('SupplierController | store', [
                'user_details' => auth()->user(),
                'message' => 'Supplier details stored successfully.',
                'request_payload' => $request->all(),
            ]);

           
            return redirect()->route('suppliers.index')->with('success', 'Supplier Added');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function edit($id)
    {
        $supplier = Supplier::find($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        try {
            $supplier = Supplier::find($id);

            $request->validate([
                'name' => 'string',
                'address' => 'string|nullable',
                'location' => 'string|nullable',
                'tel' => 'string|nullable',
                'phone' => 'string|nullable',
                'email' => 'string|nullable',
            ]);

            $supplier->update($request->all());

            Log::info('SupplierController | update', [
                'user_details' => auth()->user(),
                'message' => 'Supplier details updated successfully.',
                'request_payload' => $request->all(),
            ]);

        
            return redirect()->back()->with('success','Successfully Updated');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function destroy($id)
    {
        try{
            $supplier = Supplier::find($id);
            $supplier->delete();
    
            Log::info('SupplierController | destroy', [
                'user_details' => auth()->user(),
                'message' => 'Supplier deleted successfully.',
                'supplier_details' => $supplier,
            ]);
            return redirect()->route('suppliers.index')->with('success', 'Successfully Deleted');
        }catch(\Exception $e){
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

       
    }

    public function supplier_search(Request $request)
    {
        try {
            $site_id = Auth::user()->site->id;
            if ($request->search) {
                $suppliers = Supplier::where('suppliers.phone', 'like', "%" . $request->search . "%")
                    ->orWhere('suppliers.name', 'like', "%" . $request->search . "%")->latest()->paginate(15);
            } else {
                $suppliers = Supplier::latest()->paginate(10);
            }

            return view('suppliers.index', compact('suppliers'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('An error occurred with id ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
    }

    }

    /**
     * Show bulk import form
     */
    public function showImportForm()
    {
        try {
            return view('suppliers.import');
        } catch (\Exception $e) {
            Log::error('SupplierController | showImportForm | Error: ' . $e->getMessage());
            Toastr::error('An error occurred while loading the import form.');
            return redirect()->route('suppliers.index');
        }
    }

    /**
     * Handle bulk import of suppliers from CSV/XLSX
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $import = new SuppliersImport();
            
            Excel::import($import, $request->file('file'));

            $stats = $import->getStats();
            $failures = $import->failures();

            if ($stats['success'] > 0) {
                Toastr::success("Successfully imported {$stats['success']} supplier(s).");
            }

            if ($stats['failed'] > 0 || count($failures) > 0) {
                $errorMessage = "Failed to import {$stats['failed']} supplier(s).";
                if (count($failures) > 0) {
                    $errorMessage .= " Please check the file format and try again.";
                }
                Toastr::warning($errorMessage);
            }

            Log::info('SupplierController | import | Bulk import completed', [
                'user_id' => Auth::id(),
                'success_count' => $stats['success'],
                'failed_count' => $stats['failed'],
            ]);

            return redirect()->route('suppliers.index')
                ->with('import_stats', $stats)
                ->with('import_failures', $failures);

        } catch (\Exception $e) {
            Log::error('SupplierController | import | Error: ' . $e->getMessage(), [
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => Auth::id(),
            ]);
            Toastr::error('An error occurred while importing suppliers. Please check the file format and try again.');
            return redirect()->back();
        }
    }

    /**
     * Download sample import template
     */
    public function downloadImportTemplate()
    {
        return Excel::download(new SuppliersImportTemplateExport(), 'suppliers_import_template.xlsx');
    }
}
