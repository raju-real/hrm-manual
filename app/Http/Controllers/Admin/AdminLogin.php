<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\TrustedDevice;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\TwoFactorSmsService;

class AdminLogin extends Controller
{
    public function __invoke(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();
        }


        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
            'status' => 'active',
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $user = auth()->user();
            // Update login time
            $user->last_login_at = now();
            $user->save();

            if($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif($user->role === 'employee') {
                return redirect()->intended(route('admin.check-in-out'));
            } else {
                return redirect()->intended(route('permission-denied'));
            }
        }

        return redirect()
            ->back()
            ->withInput($request->only('email', 'remember'))
            ->with('message', 'Email or Password not matched!');
    }
}
