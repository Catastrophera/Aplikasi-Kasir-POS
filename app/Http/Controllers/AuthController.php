<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Password disimpan di .env sebagai APP_POS_PASSWORD
    private function getPassword(): string
    {
        return config('app.pos_password', 'cheframa123');
    }

    public function showLogin()
    {
        if (Session::get('pos_authenticated')) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        if ($request->password === $this->getPassword()) {
            Session::put('pos_authenticated', true);
            return redirect('/')->with('success', 'Selamat datang kembali! 👋');
        }

        return back()->withErrors(['password' => 'Password salah. Coba lagi.']);
    }

    public function logout()
    {
        Session::forget('pos_authenticated');
        return redirect('/login')->with('success', 'Berhasil keluar.');
    }
}
