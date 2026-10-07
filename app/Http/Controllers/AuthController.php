<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Proses Login Perangkat Desa
     */
    public function proseslogin(Request $request)
    {
        if (Auth::guard('perangkat')->attempt(['nik' => $request->nik, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return redirect('/')->with(['warning' => 'NIK / Password Salah']);
    }

    /**
     * Proses Logout Perangkat Desa
     */
    public function proseslogout(Request $request)
    {
        if (Auth::guard('perangkat')->check()) {
            Auth::guard('perangkat')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/');
    }

    /**
     * Proses Login Admin Panel
     */
    public function prosesloginadmin(Request $request)
    {
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('dashboardadmin');
        }

        return redirect('/panel')->with(['warning' => 'Email atau Password Salah']);
    }

    /**
     * Proses Logout Admin Panel
     */
    public function proseslogoutadmin(Request $request)
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect('/panel');
    }
}