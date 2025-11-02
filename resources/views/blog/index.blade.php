@extends('layouts.blog')

@section('title', 'Blog - José Rafael Camejo')

@section('content')
<div class="blog-container">
    <div class="blog-grid">
        <div class="blog-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>Blog</h1>
                
                @if(request('category') || request('tag') || request('search'))
                    <div>
                        <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-secondary btn-sm">
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
                @foreach($posts as $post)
                    <article class="blog-post-card">
                        @if($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" class="blog-post-image" alt="{{ $post->title }}">
                        @endif
                        
                        <div class="blog-post-content">
                            <h2 class="blog-post-title">
                                <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $post->slug]) }}">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            <div class="blog-post-excerpt">
                                {{ Str::limit(strip_tags($post->content), 150) }}
                            </div>
                            
                            @if($post->tags->count() > 0)
                                <div class="mb-3">
                                    @foreach($post->tags as $tag)
                                        <a href="{{ route('blog.tag', ['locale' => app()->getLocale(), 'slug' => $tag->slug]) }}" class="badge badge-secondary text-decoration-none me-1">
                                            {{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            
                            <div class="blog-post-meta">
                                <div class="blog-post-date">
                                    <i class="fas fa-calendar"></i>
                                    {{ $post->published_at->format('d/m/Y') }}
                                </div>
                                @if($post->category)
                                    <a href="{{ route('blog.category', ['locale' => app()->getLocale(), 'slug' => $post->category->slug]) }}" class="blog-post-category">
                                        {{ $post->category->name }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach

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

        <div class="blog-sidebar">
            <!-- Search -->
            <div class="sidebar-section">
                <h3 class="sidebar-title"><i class="fas fa-search"></i> Buscar</h3>
                <form method="GET" action="{{ route('blog.index', ['locale' => app()->getLocale()]) }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Buscar posts..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Categories -->
            @if($categories->count() > 0)
                <div class="sidebar-section">
                    <h3 class="sidebar-title"><i class="fas fa-folder"></i> Categorias</h3>
                    <ul class="sidebar-list">
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('blog.category', ['locale' => app()->getLocale(), 'slug' => $category->slug]) }}">
                                    {{ $category->name }}
                                    <span class="post-count">{{ $category->posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Tags -->
            @if($tags->count() > 0)
                <div class="sidebar-section">
                    <h3 class="sidebar-title"><i class="fas fa-tags"></i> Tags</h3>
                    <div>
                        @foreach($tags as $tag)
                            <a href="{{ route('blog.tag', ['locale' => app()->getLocale(), 'slug' => $tag->slug]) }}" class="badge badge-secondary text-decoration-none me-1 mb-1">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recent Posts -->
            @if($recentPosts->count() > 0)
                <div class="sidebar-section">
                    <h3 class="sidebar-title"><i class="fas fa-clock"></i> Posts Recentes</h3>
                    <ul class="sidebar-list">
                        @foreach($recentPosts as $recentPost)
                            <li>
                                <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $recentPost->slug]) }}">
                                    {{ Str::limit($recentPost->title, 50) }}
                                </a>
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar"></i> {{ $recentPost->published_at->format('d/m/Y') }}
                                </small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection