<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role-lists-roles|add-role-button-roles|edit-role-button-roles|update-role-button-roles', ['only' => ['rolesPermissionLists']]);
        $this->middleware('permission:add-role-button-roles', ['only' => ['storeRoleName']]);
        $this->middleware('permission:edit-role-button-roles', ['only' => ['changeRolePermissions']]);
        $this->middleware('permission:update-role-button-roles', ['only' => ['updateRolePermissions']]);
    }

    #Roles and Permissions lists
    public function rolesPermissionLists(Request $request)
    {
        try {
            if ($request->ajax()) {
                $rolesandpermissions = Role::select('*')->with(['permissions' => function ($query) {
                    $query->limit(5)->orderBy('id', 'ASC');
                }])->orderBy('id', 'ASC');
                return DataTables::of($rolesandpermissions)
                    ->addIndexColumn()
                    ->editColumn('name', function ($row) {
                        $name = $row->name ?? 'N/A';
                        $color = '#' . substr(md5($name), 0, 6);
                        return view('components.circle-name', ['name' => $name, 'color' => $color]);
                    })
                    ->editColumn('permissions', function ($row) {
                        $badges = '';
                        $permissions = $row->permissions;
                        if ($permissions->isEmpty()) {
                            return '<a href="javascript:void(0)" class="edit-comman-btn">No Permissions</a></div>';
                        }
                        foreach ($permissions as $permission) {
                            $badges .= '<span class="badge text-dark me-1">' . $permission->name . '</span>';
                        }
                        return $badges;
                    })
                    ->addColumn('action', function ($row) {
                        return '<div class="action-btn-box"><a href="' . route('change.role.permissions', ['roleId' => $row->id]) . '" class="edit-comman-btn">Edit</a></div>';
                    })
                    ->rawColumns(['name', 'permissions', 'action'])
                    ->make(true);
            }
            return view('RolePermissions.index');
        } catch (\Throwable $e) {
            Log::channel('exception')->error('listMembers: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch team members', 'alert-type' => 'error']);
        }
    }

    #add Role View page
    public function addRole()
    {
        return view('RolePermissions.add-role');
    }

    #Save roles
    public function storeRoleName(Request $request)
    {
        Log::channel('daily')->info('storeRoleName: Try to create role with request data: ' . json_encode($request->all()) . ' by the user ' . Auth::user()->name);
        try {
            $validate = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:roles,name',
            ], [
                'name.required' => 'Please enter role name',
            ]);
            if ($validate->fails()) {
                return redirect()->back()->with(['alert-type' => 'error', 'message' => $validate->errors()->first()])->withInput();
            }
            $role = Role::create(['name' => $request->name]);
            if ($role) {
                return redirect()->route('roles')->with(['alert-type' => 'success', 'message' => 'Role created successfully']);
            }
        } catch (\Throwable $e) {
            Log::channel('exception')->error('storeRoleName: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to add role name', 'alert-type' => 'error']);
        }
    }

    #permission list according to roles
    public function changeRolePermissions($id)
    {
        Log::channel('daily')->info('changeRolePermissions: Attempting to change role permissions ID: ' . $id . ' by the user ' . Auth::user()->name);
        try {
            $grouped = [];
            $role = Role::with('permissions')->find($id);
            if (strtolower($role->name) == strtolower('admin')) {
                return redirect()->back()->with(['message' => 'You cannot edit or update admin permissions', 'alert-type' => 'error']);
            }
            if (!$role) {
                return redirect()->back()->with(['message' => 'Role not found', 'alert-type' => 'error']);
            }
            #permission lists
            $permissionslist = Permission::get();
            #permissions list assigned
            $assignedPermissions = $role->permissions->pluck('name')->toArray();
            // check permissions exist
            if ($permissionslist && $permissionslist->isNotEmpty()) {
                foreach ($permissionslist as $permission) {
                    $name = $permission->name ?? '';
                    if (!empty($name)) {
                        $parts = explode('-', $name);
                        $last = end($parts);
                        $grouped[$last][] = $name;
                    }
                }
            }
            return view('RolePermissions.permission', ['usersPermissions' => $grouped, 'role' => $role, 'assignedPermissions' => $assignedPermissions ?? []]);
        } catch (\Throwable $e) {
            Log::channel('exception')->error('changeRolePermissions: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'Failed to fetch permissions', 'alert-type' => 'error']);
        }
    }

    #update Roles and permissions
    public function updateRolePermissions(Request $request, $id)
    {
        Log::channel('daily')->info('updateRolePermissions: Update role\'s permissions of ID ' . $id . ' by the user ' . Auth::user()->name . ' with details: ' . json_encode($request->all()));
        try {
            $role = Role::findOrFail($id);
            $validate = Validator::make($request->all(), [
                'permissions' => 'nullable|array',
                'permissions.*' => 'string|exists:permissions,name',
            ]);
            if ($validate->fails()) {
                return back()->with('message', $validate->errors()->first())->with('alert-type', 'error');
            }
            $permissions = $request->input('permissions', []);
            $role->syncPermissions($permissions);
            return redirect()->route('roles')->with(['message' => 'Permissions updated successfully!', 'alert-type' => 'success']);
        } catch (\Throwable $e) {
            Log::channel('exception')->error('updateRolePermissions: ' . $e->getMessage());
            return back()->with('message', 'Failed to update permissions')->with('alert-type', 'error');
        }
    }
}
