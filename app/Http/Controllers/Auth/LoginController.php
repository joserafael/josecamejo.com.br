<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Mostrar o formulário de login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Processar o login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => __('messages.validation.email_required'),
            'email.email' => __('messages.validation.email_invalid'),
            'password.required' => __('messages.validation.password_required'),
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Verificar se o usuário é admin
            if (Auth::user()->is_admin) {
                return redirect()->intended(route('admin.dashboard'));
            } else {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => __('messages.validation.access_denied'),
                ]);
            }
        }

        throw ValidationException::withMessages([
            'email' => __('messages.validation.login_invalid'),
        ]);
    }

    /**
     * Fazer logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
