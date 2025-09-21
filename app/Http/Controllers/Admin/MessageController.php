<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index(Request $request)
    {
        $query = Message::query();

        // Filtros
        if ($request->has('status')) {
            switch ($request->status) {
                case 'unread':
                    $query->unread();
                    break;
                case 'unanswered':
                    $query->unanswered();
                    break;
                case 'replied':
                    $query->where('is_replied', true);
                    break;
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->orderBy('created_at', 'desc')->paginate(15);

        $data = [
            'pageTitle' => 'Gerenciar Mensagens',
            'pageDescription' => 'Lista de todas as mensagens recebidas',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Mensagens', 'url' => '']
            ],
            'messages' => $messages,
            'filters' => $request->only(['status', 'search'])
        ];

        return view('admin.messages.index', $data);
    }

    /**
     * Show the form for creating a new message.
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Nova Mensagem',
            'pageDescription' => 'Criar uma nova mensagem',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Mensagens', 'url' => route('admin.messages.index')],
                ['title' => 'Nova Mensagem', 'url' => '']
            ]
        ];

        return view('admin.messages.create', $data);
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
        ]);

        Message::create($validated);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Mensagem criada com sucesso!');
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Marcar como lida quando visualizada
        if (!$message->is_read) {
            $message->markAsRead();
        }

        $data = [
            'pageTitle' => 'Visualizar Mensagem',
            'pageDescription' => 'Detalhes da mensagem',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Mensagens', 'url' => route('admin.messages.index')],
                ['title' => 'Visualizar', 'url' => '']
            ],
            'message' => $message
        ];

        return view('admin.messages.show', $data);
    }

    /**
     * Show the form for editing the specified message.
     */
    public function edit(Message $message)
    {
        $data = [
            'pageTitle' => 'Editar Mensagem',
            'pageDescription' => 'Editar informações da mensagem',
            'breadcrumbs' => [
                ['title' => 'Admin', 'url' => route('admin.dashboard')],
                ['title' => 'Mensagens', 'url' => route('admin.messages.index')],
                ['title' => 'Editar', 'url' => '']
            ],
            'message' => $message
        ];

        return view('admin.messages.edit', $data);
    }

    /**
     * Update the specified message in storage.
     */
    public function update(Request $request, Message $message)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'is_read' => 'boolean',
            'is_replied' => 'boolean',
        ]);

        $message->update($validated);

        return redirect()->route('admin.messages.index')
            ->with('success', 'Mensagem atualizada com sucesso!');
    }

    /**
     * Remove the specified message from storage.
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')
            ->with('success', 'Mensagem excluída com sucesso!');
    }

    /**
     * Reply to a message.
     */
    public function reply(Request $request, Message $message)
    {
        $validated = $request->validate([
            'reply' => 'required|string'
        ]);

        $message->markAsReplied($validated['reply']);

        // Aqui você pode implementar o envio de email se necessário
        // Mail::to($message->email)->send(new MessageReply($message, $validated['reply']));

        return redirect()->route('admin.messages.show', $message)
            ->with('success', 'Resposta enviada com sucesso!');
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(Message $message)
    {
        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Toggle read status.
     */
    public function toggleRead(Message $message)
    {
        $message->update(['is_read' => !$message->is_read]);

        return response()->json(['success' => true, 'is_read' => $message->is_read]);
    }
}
