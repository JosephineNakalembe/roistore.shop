<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $adminEmail = 'josephinenakalembe33@gmail.com';
        $adminPassword = '230303';

        if ($credentials['email'] === $adminEmail) {
            if ($credentials['password'] !== $adminPassword) {
                return back()->withErrors(['email' => 'Invalid login credentials'])->onlyInput('email');
            }

            $user = User::updateOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => 'Administrator',
                    'password' => $adminPassword,
                    'role' => 'admin',
                    'status' => 'active',
                ]
            );

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Invalid login credentials'])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->status !== 'active') {
            Auth::logout();
            return back()->withErrors(['email' => 'Your account is not active.']);
        }

        $isAdminEmail = $user->email === $adminEmail;
        return redirect()->intended($isAdminEmail ? route('admin.dashboard') : route('shop.index'));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $adminEmail = 'josephinenakalembe33@gmail.com';

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        if ($data['email'] === $adminEmail) {
            return redirect()->route('login')->withErrors(['email' => 'Admin registration is disabled. Please log in instead.']);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'user',
            'status' => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('shop.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
