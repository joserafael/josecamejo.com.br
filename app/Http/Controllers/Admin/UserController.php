<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'Gerenciar Usuários',
            'pageDescription' => 'Lista de todos os usuários do sistema',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Usuários', 'url' => '']
            ]
        ];

        return view('admin.users.index', $data);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Novo Usuário',
            'pageDescription' => 'Criar um novo usuário no sistema',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Usuários', 'url' => route('admin.users.index')],
                ['title' => 'Novo Usuário', 'url' => '']
            ]
        ];

        return view('admin.users.create', $data);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // Validação e criação do usuário
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        $data = [
            'pageTitle' => 'Visualizar Usuário',
            'pageDescription' => 'Detalhes do usuário',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Usuários', 'url' => route('admin.users.index')],
                ['title' => 'Visualizar', 'url' => '']
            ]
        ];

        return view('admin.users.show', $data);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(string $id)
    {
        $data = [
            'pageTitle' => 'Editar Usuário',
            'pageDescription' => 'Editar informações do usuário',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Usuários', 'url' => route('admin.users.index')],
                ['title' => 'Editar', 'url' => '']
            ]
        ];

        return view('admin.users.edit', $data);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validação e atualização do usuário
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(string $id)
    {
        // Implementar lógica de exclusão aqui
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}