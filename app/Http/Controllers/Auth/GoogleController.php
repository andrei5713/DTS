<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        // Use stateless() only in local development. Remove in production.
        return Socialite::driver('google')->stateless()->redirect();
    }

    /**
     * Obtain the user information from Google and log them in.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            // Use stateless() only in dev if session issues exist
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Find or create user
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    // You can also save $googleUser->getAvatar(), $googleUser->getToken(), etc.
                    'password' => null, // Optional: don't store blank password
                ]
            );

            // Log in the user
            Auth::login($user);

            // Regenerate session to prevent session fixation
            $request->session()->regenerate();

            return redirect()->intended('/home');
        } catch (Exception $e) {
            report($e); // ✅ Logs the error in production

            return redirect('/login')->with('error', 'Google login failed. Please try again.');
        }
    }
}