<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // Show all users
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();

        return view('pages.gebruikers', [
            'users' => $users,
        ]);
    }

    // Show form to create new user
    public function create()
    {
        return view('admin.users.create', [
            'roles'=> Role::all(),
        ]);
    }

    // Save new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
            'province' => 'nullable|in:Vlaams-Brabant,West-Vlaanderen,Oost-Vlaanderen,Limburg,Antwerpen',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'province' => $request->input('province'),
        ]);

        return redirect()->route('gebruikers')->with('success', 'Gebruiker aangemaakt!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    // Show form to edit user
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::all()
        ]);
    }

    // Save edited user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role_id' => 'required|exists:roles,id',
            'province' => 'nullable|in:Vlaams-Brabant,West-Vlaanderen,Oost-Vlaanderen,Limburg,Antwerpen',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->province = $request->input('province');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('gebruikers')->with('success', 'Gebruiker bijgewerkt!');
    }

    // Delete user
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('gebruikers')->with('success', 'Gebruiker verwijderd!');
    }
}
