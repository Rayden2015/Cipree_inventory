<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
        $this->middleware(['auth', 'permission:view-permission'])->only('show');
        $this->middleware(['auth', 'permission:add-permission'])->only('create');
        $this->middleware(['auth', 'permission:view-permission'])->only('index');
        $this->middleware(['auth', 'permission:edit-permission'])->only('edit');
    }
  
    public function index()
    {   
        $permissions = Permission::all();

        return view('permissions.index', [
            'permissions' => $permissions
        ]);
    }

    
    public function create() 
    {   
        return view('permissions.create');
    }

    
    public function store(Request $request)
    {   
        $request->validate([
            'name' => 'required|unique:users,name'
        ]);

        Permission::create($request->only('name'));

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission created successfully.'));
    }

    
    public function edit(Permission $permission)
    {
        return view('permissions.edit', [
            'permission' => $permission
        ]);
    }

   
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,'.$permission->id
        ]);

        $permission->update($request->only('name'));

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission updated successfully.'));
    }

   
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')
            ->withSuccess(__('Permission deleted successfully.'));
    }
}
