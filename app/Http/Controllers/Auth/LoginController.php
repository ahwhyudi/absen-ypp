<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function authenticate(Request $request)
    {

        // $user = User::where('username', $request->username)->first();

        // dd($user);
        // Validasi
        $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Ambil credential
        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        // Coba login
        if (Auth::attempt($credentials)) {

            // Regenerasi session untuk keamanan
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect sesuai role
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('manager')) {
                return redirect()->route('manager.dashboard');
            }

            if ($user->hasRole('employee')) {
                return redirect()->route('attendance.index');
            }

            // Kalau tidak punya role
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'login' => 'Role user belum ditentukan.',
                ]);
        }

        // Username / Password salah
        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'login' => 'Username atau Password salah.',
            ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
