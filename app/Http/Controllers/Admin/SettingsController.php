<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function updateAssets(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|file|mimetypes:image/svg+xml,text/html,text/xml,application/xml',
            'favicon' => 'nullable|file|mimetypes:image/svg+xml,text/html,text/xml,application/xml',
        ]);

        if ($request->hasFile('logo')) {
            $request->file('logo')->move(public_path(), 'logo.svg');
        }

        if ($request->hasFile('favicon')) {
            $request->file('favicon')->move(public_path(), 'favicon.svg');
        }

        return back()->with('assetsSuccess', 'Site assets updated successfully! (You may need to clear your browser cache to see the changes).');
    }

    public function updateCredentials(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $admin = auth('admin')->user();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'The provided current password does not match our records.']);
        }


        $admin->password = Hash::make($request->password);
        $admin->save();

        return back()->with('passwordSuccess', 'Password updated successfully.');
    }
}