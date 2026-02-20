<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): View|RedirectResponse
    {
        $сredentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::guard('admin')->attempt($сredentials, $request->boolean('remember'))) {
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => __('admin.invalid_credentials'),
        ]);
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('admin')->logout();

        return redirect('/admin');
    }
}
