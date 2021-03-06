<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response|\Inertia\Response
     */
    public function index()
    {
        return inertia('Admin/Roles/Index', [
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validateWithBag('createRole', [
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array',
            'number_of_bookings_per_period' => 'integer|gt:0|nullable',
            'number_of_days_per_period' => 'integer|gt:0|nullable'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
            'number_of_bookings_per_period' => $request->number_of_bookings_per_period,
            'number_of_days_per_period' => $request->number_of_days_per_period
        ]);

        $role->syncPermissions($request->permissions);

        return redirect(route('admin.roles.index'))->with('flash', ['new' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  Role  $role
     * @return RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        $request->validateWithBag('updateRole', [
            'name' => 'required|string|max:255',
            'permissions' => 'required|array',
            'number_of_bookings_per_period' => 'integer|gt:0|nullable',
            'number_of_days_per_period' => 'integer|gt:0|nullable'
        ]);

        $role->name = $request->name;
        $role->number_of_bookings_per_period = $request->number_of_bookings_per_period;
        $role->number_of_days_per_period = $request->number_of_days_per_period;

        $role->save();
        $role->syncPermissions($request->permissions);

        return redirect(route('admin.roles.index'))->with('flash', ['updated' => $role]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role  $role
     * @return RedirectResponse
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return redirect(route('admin.roles.index'));
    }
}
