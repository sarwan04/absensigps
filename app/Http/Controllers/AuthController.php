<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        if (Auth::guard('pegawai')->attempt(['nip' => $request->nip, 'password' => $request->password])) {
            return redirect('/dashboard');
        } else {
            return redirect('/')->with(['warning' => 'NIP / Password Salah']);
        }
    }

    public function proseslogout()
    {
        if (Auth::guard('pegawai')->check()) {
            Auth::guard('pegawai')->logout();
            return redirect('/');
        }
    }

    public function proseslogoutadmin()
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            return redirect('/admin');
        }
    }

    public function prosesloginadmin(Request $request)
    {
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/admin/dashboardadmin');
        } else {
            return redirect('/admin')->with(['warning' => 'Username atau Password Salah']);
        }
    }
}
