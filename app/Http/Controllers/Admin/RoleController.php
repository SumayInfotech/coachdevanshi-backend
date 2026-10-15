<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use Spatie\Permission\Models\Role;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use App\DataTables\RoleDataTable;


class RoleController extends Controller
{
    // protected $viewPrefix = 'admin.services.hotels-resorts';
    // protected $routePrefix = 'admin.services.hotels-resorts';
    protected $viewPrefix = 'admin.role';
    protected $routePrefix = 'admin.roles';
    private $viewPermission;
    private $addPermission;
    private $editPermission;
    private $deletePermission;

    public function __construct()
    {
        $this->viewPermission = hasPermission('view_role');
        $this->addPermission = hasPermission('add_role');
        $this->editPermission = hasPermission('edit_role');
        $this->deletePermission = hasPermission('delete_role');
        view()->share([
            'viewPrefix' => $this->viewPrefix,
            'routePrefix' => $this->routePrefix,
            'addPermission' =>  $this->addPermission,
            'moduleFullName' => 'Role Management',
            'moduleShortName' => 'Role',
        ]);
    }

    public function index(RoleDataTable $dataTable)
    {
        if (!$this->viewPermission) {
            abort(403, 'Unauthorized action.');
        }
        $permissions = Permission::all()->groupBy('group_name');
        return $dataTable->render($this->viewPrefix.'.index',compact('permissions'));
        //return view($this->viewPrefix.'.index', compact('permissions'));
    }

    

    
    public function create()
    {
        if (!$this->addPermission) {
            abort(403, 'Unauthorized action.');
        } 

        // Get permissions grouped by 'group_name'
        $permissions = Permission::select('name', 'display_name', 'group_name')
                                    ->orderBy('group_name')
                                    ->get()
                                    ->groupBy('group_name');
       
        return view('admin.role.create', compact('permissions'));
    }
    

    public function store(Request $request)
    {
        if (!$this->addPermission) {
            abort(403, 'Unauthorized action.');
        }  


        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'status' => 'required|in:Active,Inactive',
            'permissions' => 'array', 
        ]);

        // Create a new role
        $role = Role::create([
            'name' => $request->name,
            'status' => $request->status, 
        ]);

        // Sync selected permissions if any
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        activity_log(
            log: 'Role',
            event: 'created',
            subject: $role,
            properties: [
                'new' => $role->toArray(),
            ]
        );
        return redirect()->route('admin.roles')->with('success', 'Role created successfully!');
    }

    public function edit($uuid)
    {
        // if (!hasPermission('edit_role')) {
        //     abort(403, 'Unauthorized action.');
        // }  
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        } 
        // $role = Role::with('permissions')->findOrFail($id);
        $role = Role::with('permissions')->where('uuid', $uuid)->firstOrFail();

        activity_log(
            log: 'Role',
            event: 'edit_opened',
            subject: $role
        );
        // $permissions = Permission::all()->groupBy('group_name');
        $permissions = Permission::select('name', 'display_name', 'group_name')
                                    ->orderBy('group_name')
                                    ->get()
                                    ->groupBy('group_name');

        return view('admin.role.edit', compact('role', 'permissions'));
    }

    // Method to handle the update
    public function update(Request $request,$uuid)
    {
        // if (!hasPermission('edit_role')) {
        //     abort(403, 'Unauthorized action.');
        // } 
        if (!$this->editPermission) {
            abort(403, 'Unauthorized action.');
        } 

        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $uuid . ',uuid', // Use UUID in validation
            'status' => 'required|in:Active,Inactive',
            'permissions' => 'required|array',
        ]);

        // $role = Role::findOrFail($id);
        // $role = Role::findOrFail($id);
        $role = Role::where('uuid', $uuid)->firstOrFail();
        $oldData = $role->getOriginal();
        $role->name = $request->input('name');
        $role->status = $request->input('status');
        $permissionIds = Permission::whereIn('name', $request->permissions)->pluck('id')->toArray();
        $role->permissions()->sync($permissionIds);
        $role->save();

        activity_log(
            log: 'Role',
            event: 'updated',
            subject: $role,
            properties: [
                'old' => $oldData,
                'new' => $role->fresh()->toArray(),
            ]
        );

        return redirect()->route('admin.roles')->with('success', 'Role updated successfully');

    }

    public function delete($uuid)
    {
        if (!$this->deletePermission) {
             return response()->json(['success' => false, 'message' => 'Unauthorized Permission Denied']);
        }
        
        try {
            // Find the role by ID
            // $role = Role::findOrFail($id);
            $role = Role::withCount('assignedUsers')->where('uuid', $uuid)->firstOrFail();
            $deletedData = $role->toArray();
            if ($role->assigned_users_count > 0) {
                return response()->json(['success' => false, 'message' => 'This role is assigned to users and cannot be deleted.']);
            }   
            $role->permissions()->detach();
            $role->delete();

            activity_log(
                log: 'Role',
                event: 'deleted',
                subject: $role,
                properties: [
                    'data' => $deletedData
                ]
            );

    
            return response()->json(['success' => true, 'message' => 'Role and associated permissions detached.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function changeStatus($uuid, Request $request)
    {
        if (!hasPermission('edit_role')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized Permission Denied']);
        }

        $role = Role::where('uuid', $uuid)->first();

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found']);
        }
        $oldStatus = $role->status;
        // Update role status
        $role->status = $request->status;
        $role->save();

        activity_log(
            log: 'Role',
            event: 'status_changed',
            subject: $role,
            properties: [
                'old' => ['status' => $oldStatus],
                'new' => ['status' => $request->status],
            ]
        );
        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }


}
