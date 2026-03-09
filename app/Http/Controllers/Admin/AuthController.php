<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required'
        ]);

        $admins = Admin::all();
        $validAdmin = null;

        foreach ($admins as $admin) {
            try {
                $decryptedUsername = Crypt::decryptString($admin->username);
                if ($decryptedUsername === $request->username) {
                    $validAdmin = $admin;
                    break;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        if ($validAdmin && Hash::check($request->password, $validAdmin->password)) {
            Auth::guard('admin')->login($validAdmin, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'username' => 'Invalid username or password'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
