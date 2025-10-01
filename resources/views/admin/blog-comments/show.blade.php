@extends('layouts.admin')

@section('title', 'Detalhes do Comentário')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Detalhes do Comentário</h3>
                    <div>
                        <a href="{{ route('admin.blog-comments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <a href="{{ route('admin.blog-comments.edit', $blogComment) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Comment Details -->
                            <div class="card">
                                <div class="card-header">
                                    <h5>Informações do Comentário</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Status:</strong></div>
                                        <div class="col-sm-9">
                                            @if($blogComment->status === 'approved')
                                                <span class="badge badge-success">Aprovado</span>
                                            @elseif($blogComment->status === 'rejected')
                                                <span class="badge badge-danger">Rejeitado</span>
                                            @else
                                                <span class="badge badge-warning">Pendente</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Post:</strong></div>
                                        <div class="col-sm-9">
                                            <a href="{{ route('admin.blog-posts.show', $blogComment->blogPost) }}" target="_blank">
                                                {{ $blogComment->blogPost->title }}
                                            </a>
                                        </div>
                                    </div>

                                    @if($blogComment->parent)
                                        <div class="row mb-3">
                                            <div class="col-sm-3"><strong>Em resposta a:</strong></div>
                                            <div class="col-sm-9">
                                                <div class="border p-2 bg-light">
                                                    <strong>{{ $blogComment->parent->author_name }}</strong><br>
                                                    <small class="text-muted">{{ $blogComment->parent->created_at->format('d/m/Y H:i') }}</small><br>
                                                    {{ Str::limit($blogComment->parent->content, 200) }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Conteúdo:</strong></div>
                                        <div class="col-sm-9">
                                            <div class="border p-3">
                                                {!! nl2br(e($blogComment->content)) !!}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Data de Criação:</strong></div>
                                        <div class="col-sm-9">{{ $blogComment->created_at->format('d/m/Y H:i:s') }}</div>
                                    </div>

                                    @if($blogComment->approved_at)
                                        <div class="row mb-3">
                                            <div class="col-sm-3"><strong>Aprovado em:</strong></div>
                                            <div class="col-sm-9">
                                                {{ $blogComment->approved_at->format('d/m/Y H:i:s') }}
                                                @if($blogComment->approvedBy)
                                                    por {{ $blogComment->approvedBy->name }}
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Replies -->
                            @if($blogComment->replies->count() > 0)
                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5>Respostas ({{ $blogComment->replies->count() }})</h5>
                                    </div>
                                    <div class="card-body">
                                        @foreach($blogComment->replies as $reply)
                                            <div class="border-left border-primary pl-3 mb-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $reply->author_name }}</strong>
                                                        @if($reply->status === 'approved')
                                                            <span class="badge badge-success badge-sm">Aprovado</span>
                                                        @elseif($reply->status === 'rejected')
                                                            <span class="badge badge-danger badge-sm">Rejeitado</span>
                                                        @else
                                                            <span class="badge badge-warning badge-sm">Pendente</span>
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('admin.blog-comments.show', $reply) }}" class="btn btn-sm btn-outline-primary">
                                                            Ver Detalhes
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    {!! nl2br(e(Str::limit($reply->content, 300))) !!}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-4">
                            <!-- Author Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5>Informações do Autor</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Nome:</strong></div>
                                        <div class="col-sm-8">{{ $blogComment->author_name }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>Email:</strong></div>
                                        <div class="col-sm-8">
                                            <a href="mailto:{{ $blogComment->author_email }}">{{ $blogComment->author_email }}</a>
                                        </div>
                                    </div>
                                    @if($blogComment->author_website)
                                        <div class="row mb-2">
                                            <div class="col-sm-4"><strong>Website:</strong></div>
                                            <div class="col-sm-8">
                                                <a href="{{ $blogComment->author_website }}" target="_blank">{{ $blogComment->author_website }}</a>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>IP:</strong></div>
                                        <div class="col-sm-8">{{ $blogComment->ip_address }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-sm-4"><strong>User Agent:</strong></div>
                                        <div class="col-sm-8">
                                            <small>{{ Str::limit($blogComment->user_agent, 100) }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5>Ações</h5>
                                </div>
                                <div class="card-body">
                                    @if($blogComment->status !== 'approved')
                                        <a href="{{ route('admin.blog-comments.approve', $blogComment) }}" 
                                           class="btn btn-success btn-block mb-2"
                                           onclick="return confirm('Aprovar este comentário?')">
                                            <i class="fas fa-check"></i> Aprovar
                                        </a>
                                    @endif

                                    @if($blogComment->status !== 'rejected')
                                        <a href="{{ route('admin.blog-comments.reject', $blogComment) }}" 
                                           class="btn btn-warning btn-block mb-2"
                                           onclick="return confirm('Rejeitar este comentário?')">
                                            <i class="fas fa-times"></i> Rejeitar
                                        </a>
                                    @endif

                                    <a href="{{ route('admin.blog-comments.edit', $blogComment) }}" class="btn btn-primary btn-block mb-2">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>

                                    <form method="POST" action="{{ route('admin.blog-comments.destroy', $blogComment) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-block" 
                                                onclick="return confirm('Excluir este comentário? Esta ação não pode ser desfeita.')">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection