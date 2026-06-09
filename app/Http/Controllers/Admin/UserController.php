<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Toont alle gebruikers 
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();

        return view('pages.gebruikers', [
            'users' => $users,
        ]);
    }

    // Toont formulier om nieuwe gebruiker aan te maken
    public function create()
    {
        return view('admin.users.create', [
            'roles'=> Role::all(),
        ]);
    }

    // Sla nieuwe gebruiker op
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


    // Toont de gespecificeerde resource.
    public function show(string $id)
    {
        //
    }

    // Toont formulier om bestaande gebruiker te bewerken
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::all()
        ]);
    }

    // Sla bewerkte gebruiker op
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
            $user->password = bcrypt($request->password); // Maakt het wachtwoord onleesbaar in database
        }

        $user->save();

        return redirect()->route('gebruikers')->with('success', 'Gebruiker bijgewerkt!');
    }

    // Verwijder gebruiker
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('gebruikers')->with('success', 'Gebruiker verwijderd!');
    }
}
