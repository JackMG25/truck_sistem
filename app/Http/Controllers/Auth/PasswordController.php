<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // La app está en español y no hay lang/es/validation.php, así que estos textos se definen aquí.
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Escribe tu clave actual.',
            'current_password.current_password' => 'La clave actual no coincide.',
            'password.required' => 'Escribe la clave nueva.',
            'password.confirmed' => 'La clave nueva y la repetición no coinciden.',
            'password.min' => 'La clave nueva debe tener al menos :min caracteres.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
