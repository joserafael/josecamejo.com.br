<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'Gerenciar Projetos',
            'pageDescription' => 'Lista de todos os projetos do portfólio',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Projetos', 'url' => '']
            ]
        ];

        return view('admin.projects.index', $data);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Novo Projeto',
            'pageDescription' => 'Adicionar um novo projeto ao portfólio',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Projetos', 'url' => route('admin.projects.index')],
                ['title' => 'Novo Projeto', 'url' => '']
            ]
        ];

        return view('admin.projects.create', $data);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        // Validação e criação do projeto
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Projeto criado com sucesso!');
    }

    /**
     * Display the specified project.
     */
    public function show(string $id)
    {
        $data = [
            'pageTitle' => 'Visualizar Projeto',
            'pageDescription' => 'Detalhes do projeto',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Projetos', 'url' => route('admin.projects.index')],
                ['title' => 'Visualizar', 'url' => '']
            ]
        ];

        return view('admin.projects.show', $data);
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(string $id)
    {
        $data = [
            'pageTitle' => 'Editar Projeto',
            'pageDescription' => 'Editar informações do projeto',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Projetos', 'url' => route('admin.projects.index')],
                ['title' => 'Editar', 'url' => '']
            ]
        ];

        return view('admin.projects.edit', $data);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validação e atualização do projeto
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(string $id)
    {
        // Implementar lógica de exclusão aqui
        
        return redirect()->route('admin.projects.index')
            ->with('success', 'Projeto excluído com sucesso!');
    }
}