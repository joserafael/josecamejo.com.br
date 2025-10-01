@extends('layouts.app')

@section('title', 'Blog')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Blog</h1>
                
                @if(request('category') || request('tag') || request('search'))
                    <div>
                        <a href="{{ route('blog.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> Limpar Filtros
                        </a>
                    </div>
                @endif
            </div>

            @if(request('category'))
                <div class="alert alert-info">
                    <i class="fas fa-folder"></i> Mostrando posts da categoria: <strong>{{ request('category') }}</strong>
                </div>
            @endif

            @if(request('tag'))
                <div class="alert alert-info">
                    <i class="fas fa-tag"></i> Mostrando posts com a tag: <strong>{{ request('tag') }}</strong>
                </div>
            @endif

            @if(request('search'))
                <div class="alert alert-info">
                    <i class="fas fa-search"></i> Resultados da busca por: <strong>{{ request('search') }}</strong>
                </div>
            @endif

            @if($posts->count() > 0)
                <div class="row">
                    @foreach($posts as $post)
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                @if($post->featured_image)
                                    <img src="{{ asset('storage/' . $post->featured_image) }}" class="card-img-top" alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="text-decoration-none">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    
                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-calendar"></i> {{ $post->published_at->format('d/m/Y') }}
                                        @if($post->category)
                                            | <i class="fas fa-folder"></i> 
                                            <a href="{{ route('blog.category', $post->category->slug) }}" class="text-decoration-none">
                                                {{ $post->category->name }}
                                            </a>
                                        @endif
                                    </p>
                                    
                                    <p class="card-text">{{ Str::limit(strip_tags($post->content), 150) }}</p>
                                    
                                    @if($post->tags->count() > 0)
                                        <div class="mb-3">
                                            @foreach($post->tags as $tag)
                                                <a href="{{ route('blog.tag', $tag->slug) }}" class="badge badge-secondary text-decoration-none me-1">
                                                    {{ $tag->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <div class="mt-auto">
                                        <a href="{{ route('blog.show', $post->slug) }}" class="btn btn-primary btn-sm">
                                            Ler mais <i class="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $posts->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h4>Nenhum post encontrado</h4>
                    <p class="text-muted">
                        @if(request('search') || request('category') || request('tag'))
                            Tente ajustar seus filtros de busca.
                        @else
                            Ainda não há posts publicados no blog.
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Search -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-search"></i> Buscar</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('blog.index') }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Buscar posts..." value="{{ request('search') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Categories -->
            @if($categories->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-folder"></i> Categorias</h5>
                    </div>
                    <div class="card-body">
                        @foreach($categories as $category)
                            <a href="{{ route('blog.category', $category->slug) }}" class="d-block text-decoration-none mb-2">
                                {{ $category->name }} ({{ $category->posts_count }})
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tags -->
            @if($tags->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-tags"></i> Tags</h5>
                    </div>
                    <div class="card-body">
                        @foreach($tags as $tag)
                            <a href="{{ route('blog.tag', $tag->slug) }}" class="badge badge-secondary text-decoration-none me-1 mb-1">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Posts -->
            @if($recentPosts->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-clock"></i> Posts Recentes</h5>
                    </div>
                    <div class="card-body">
                        @foreach($recentPosts as $recentPost)
                            <div class="mb-3">
                                <h6>
                                    <a href="{{ route('blog.show', $recentPost->slug) }}" class="text-decoration-none">
                                        {{ Str::limit($recentPost->title, 50) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> {{ $recentPost->published_at->format('d/m/Y') }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection