<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended($request->user()->is_admin ? route('admin.opportunities.index') : route('dashboard'));
        }

        return back()->withErrors(['email' => 'Email or password is incorrect.'])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->merge(['phone' => preg_replace('/[\s-]/', '', (string) $request->input('phone'))]);
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => ['nullable', 'regex:/^(\+?63|0)9\d{9}$/'],
            'barangay' => ['required', Rule::in(Catalog::barangays())],
            'identity' => ['required', Rule::in(array_keys(Catalog::IDENTITIES))],
            'interests' => 'required|array|min:1',
            'interests.*' => [Rule::in(Catalog::CATEGORIES)],
        ], [
            'interests.required' => 'Pick at least one category.',
            'phone.regex' => 'Use a mobile number like 0917 123 4567, or leave it blank.',
        ]);

        $user = User::create($data);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
