<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Toont de bewerkpagina van het profiel van de gebruiker.
    public function edit(Request $request): View
    {
        return view('userzone.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Update de gebruikers gegevens in de database.
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated()); // Vul het gebruikersmodel met de gevalideerde gegevens, nog niet in database opgeslagen

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null; // Als het e-mailadres is gewijzigd, reset en gebruiker moet opnieuw verifiëren
        }

        $request->user()->save(); // Sla de wijzigingen op in de database

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Verwijder gebruiker
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'], // Komt de wachtwoord overeen met het huidige wachtwoord?
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete(); // Delete de gebruiker uit de database

        $request->session()->invalidate(); // Ongeldig maken van de huidige sessie, zodat deze niet meer gebruikt kan worden
        $request->session()->regenerateToken(); // Regenereren van het CSRF-token om beveiligingsrisico's te voorkomen

        return Redirect::to('/');
    }
}
