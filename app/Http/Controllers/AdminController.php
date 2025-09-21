<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Middleware de autenticação pode ser adicionado aqui
        // $this->middleware('auth');
        // $this->middleware('admin'); // Middleware customizado para verificar se é admin
    }

    /**
     * Show the admin dashboard.
     */
    public function dashboard()
    {
        // Dados para o dashboard
        $data = [
            'pageTitle' => 'Dashboard',
            'pageDescription' => 'Visão geral do sistema administrativo',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Dashboard', 'url' => '']
            ],
            'unreadMessages' => 3, // Exemplo de dados dinâmicos
        ];

        return view('admin.dashboard', $data);
    }

    /**
     * Show skills management page.
     */
    public function skills()
    {
        $data = [
            'pageTitle' => 'Gerenciar Habilidades',
            'pageDescription' => 'Lista de todas as habilidades técnicas',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Habilidades', 'url' => '']
            ]
        ];

        return view('admin.skills.index', $data);
    }

    /**
     * Show create skill form.
     */
    public function createSkill()
    {
        $data = [
            'pageTitle' => 'Nova Habilidade',
            'pageDescription' => 'Adicionar uma nova habilidade técnica',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Habilidades', 'url' => route('admin.skills.index')],
                ['title' => 'Nova Habilidade', 'url' => '']
            ]
        ];

        return view('admin.skills.create', $data);
    }

    /**
     * Store a new skill.
     */
    public function storeSkill(Request $request)
    {
        // Implementar lógica de criação
        return redirect()->route('admin.skills.index')->with('success', 'Habilidade criada com sucesso!');
    }

    /**
     * Show edit skill form.
     */
    public function editSkill($id)
    {
        $data = [
            'pageTitle' => 'Editar Habilidade',
            'pageDescription' => 'Editar informações da habilidade',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Habilidades', 'url' => route('admin.skills.index')],
                ['title' => 'Editar', 'url' => '']
            ]
        ];

        return view('admin.skills.edit', $data);
    }

    /**
     * Update skill.
     */
    public function updateSkill(Request $request, $id)
    {
        // Implementar lógica de atualização
        return redirect()->route('admin.skills.index')->with('success', 'Habilidade atualizada com sucesso!');
    }

    /**
     * Delete skill.
     */
    public function destroySkill($id)
    {
        // Implementar lógica de exclusão
        return redirect()->route('admin.skills.index')->with('success', 'Habilidade excluída com sucesso!');
    }

    /**
     * Show messages page.
     */
    public function messages()
    {
        $data = [
            'pageTitle' => 'Mensagens',
            'pageDescription' => 'Mensagens recebidas através do formulário de contato',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Mensagens', 'url' => '']
            ]
        ];

        return view('admin.messages.index', $data);
    }

    /**
     * Show specific message.
     */
    public function showMessage($id)
    {
        $data = [
            'pageTitle' => 'Visualizar Mensagem',
            'pageDescription' => 'Detalhes da mensagem',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Mensagens', 'url' => route('admin.messages.index')],
                ['title' => 'Visualizar', 'url' => '']
            ]
        ];

        return view('admin.messages.show', $data);
    }

    /**
     * Reply to message.
     */
    public function replyMessage(Request $request, $id)
    {
        // Implementar lógica de resposta
        return redirect()->route('admin.messages.show', $id)->with('success', 'Resposta enviada com sucesso!');
    }

    /**
     * Delete message.
     */
    public function destroyMessage($id)
    {
        // Implementar lógica de exclusão
        return redirect()->route('admin.messages.index')->with('success', 'Mensagem excluída com sucesso!');
    }

    /**
     * Show general settings page.
     */
    public function settingsGeneral()
    {
        $data = [
            'pageTitle' => 'Configurações Gerais',
            'pageDescription' => 'Configurações do sistema',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Configurações', 'url' => '']
            ]
        ];

        return view('admin.settings.general', $data);
    }

    /**
     * Show admin profile page.
     */
    public function profile()
    {
        $data = [
            'pageTitle' => 'Meu Perfil',
            'pageDescription' => 'Gerencie suas informações pessoais',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Meu Perfil', 'url' => '']
            ]
        ];

        return view('admin.profile', $data);
    }

    /**
     * Show admin settings page.
     */
    public function settings()
    {
        $data = [
            'pageTitle' => 'Configurações',
            'pageDescription' => 'Configure as opções do sistema',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Configurações', 'url' => '']
            ]
        ];

        return view('admin.settings', $data);
    }

    /**
     * Show notifications page.
     */
    public function notifications()
    {
        $data = [
            'pageTitle' => 'Notificações',
            'pageDescription' => 'Todas as suas notificações',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Notificações', 'url' => '']
            ]
        ];

        return view('admin.notifications', $data);
    }

    /**
     * Show analytics page.
     */
    public function analytics()
    {
        $data = [
            'pageTitle' => 'Analytics',
            'pageDescription' => 'Estatísticas e métricas do site',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Analytics', 'url' => '']
            ]
        ];

        return view('admin.analytics', $data);
    }

    /**
     * Show backup page.
     */
    public function backup()
    {
        $data = [
            'pageTitle' => 'Backup',
            'pageDescription' => 'Gerencie backups do sistema',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Backup', 'url' => '']
            ]
        ];

        return view('admin.backup', $data);
    }

    /**
     * Show logs page.
     */
    public function logs()
    {
        $data = [
            'pageTitle' => 'Logs do Sistema',
            'pageDescription' => 'Visualize os logs de atividade',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Logs', 'url' => '']
            ]
        ];

        return view('admin.logs', $data);
    }

    /**
     * Show help page.
     */
    public function help()
    {
        $data = [
            'pageTitle' => 'Ajuda',
            'pageDescription' => 'Central de ajuda e documentação',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Ajuda', 'url' => '']
            ]
        ];

        return view('admin.help', $data);
    }

    /**
     * Show support page.
     */
    public function support()
    {
        $data = [
            'pageTitle' => 'Suporte',
            'pageDescription' => 'Entre em contato com o suporte técnico',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Suporte', 'url' => '']
            ]
        ];

        return view('admin.support', $data);
    }
}