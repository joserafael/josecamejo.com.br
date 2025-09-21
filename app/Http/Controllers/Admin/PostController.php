<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'Gerenciar Posts',
            'pageDescription' => 'Lista de todos os posts do blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Posts', 'url' => '']
            ]
        ];

        return view('admin.posts.index', $data);
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Novo Post',
            'pageDescription' => 'Criar um novo post para o blog',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Posts', 'url' => route('admin.posts.index')],
                ['title' => 'Novo Post', 'url' => '']
            ]
        ];

        return view('admin.posts.create', $data);
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
        // Validação e criação do post
        // Implementar lógica de criação aqui
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post criado com sucesso!');
    }

    /**
     * Display the specified post.
     */
    public function show(string $id)
    {
        $data = [
            'pageTitle' => 'Visualizar Post',
            'pageDescription' => 'Detalhes do post',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Posts', 'url' => route('admin.posts.index')],
                ['title' => 'Visualizar', 'url' => '']
            ]
        ];

        return view('admin.posts.show', $data);
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(string $id)
    {
        $data = [
            'pageTitle' => 'Editar Post',
            'pageDescription' => 'Editar informações do post',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Posts', 'url' => route('admin.posts.index')],
                ['title' => 'Editar', 'url' => '']
            ]
        ];

        return view('admin.posts.edit', $data);
    }

    /**
     * Update the specified post in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validação e atualização do post
        // Implementar lógica de atualização aqui
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post atualizado com sucesso!');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(string $id)
    {
        // Implementar lógica de exclusão aqui
        
        return redirect()->route('admin.posts.index')
            ->with('success', 'Post excluído com sucesso!');
    }
}