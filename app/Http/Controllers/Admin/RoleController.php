<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create($request->only('name'));

        return redirect()->route('admin.roles.index')->with('success', 'Rol aangemaakt!');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Kan rol niet verwijderen — er zijn nog gebruikers met deze rol!');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', 'Rol verwijderd!');
    }
}
