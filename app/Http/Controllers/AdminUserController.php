<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    public function __construct()
    {
        // Middleware será aplicado nas rotas
    }

    /**
     * Verificar se o usuário é admin
     */
    private function checkAdminAccess()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta área.');
        }
    }

    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $this->checkAdminAccess();
        $query = User::query();

        // Filtro de busca
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo de usuário
        if ($request->filled('type')) {
            $type = $request->get('type');
            if ($type === 'admin') {
                $query->where('is_admin', true);
            } elseif ($type === 'user') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->checkAdminAccess();
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $this->checkAdminAccess();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'is_admin' => ['boolean'],
        ], [
            'name.required' => __('messages.validation.name_required'),
            'email.required' => __('messages.validation.email_required'),
            'email.email' => __('messages.validation.email_invalid'),
            'email.unique' => __('messages.validation.email_unique'),
            'password.required' => __('messages.validation.password_required'),
            'password.confirmed' => __('messages.validation.password_confirmed'),
            'is_admin.boolean' => __('messages.validation.is_admin_boolean'),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.success.user_created'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->checkAdminAccess();
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->checkAdminAccess();
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->checkAdminAccess();
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'is_admin' => ['boolean'],
        ], [
            'name.required' => __('messages.validation.name_required'),
            'email.required' => __('messages.validation.email_required'),
            'email.email' => __('messages.validation.email_invalid'),
            'email.unique' => __('messages.validation.email_unique'),
            'is_admin.boolean' => __('messages.validation.is_admin_boolean'),
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.success.user_updated'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->checkAdminAccess();
        
        // Previne que o admin delete a si mesmo
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', __('messages.error.cannot_delete_own_account'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.success.user_deleted'));
    }

    /**
     * Show the form for changing user password.
     */
    public function changePasswordForm(User $user)
    {
        $this->checkAdminAccess();
        return view('admin.users.change-password', compact('user'));
    }

    /**
     * Update the user's password.
     */
    public function changePassword(Request $request, User $user)
    {
        $this->checkAdminAccess();
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.required' => __('messages.validation.password_required'),
            'password.confirmed' => __('messages.validation.password_confirmed'),
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', __('messages.success.password_changed'));
    }
}