@extends('layouts.blog')

@section('title', $category->name . ' - Blog')

@section('content')
    <div class="blog-container">
        <div class="blog-grid">
            <div class="blog-content">
                <div class="mb-4">
                    <h1><i class="fas fa-folder"></i> {{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-muted">{{ $category->description }}</p>
                    @endif
                </div>

                @if($posts->count() > 0)
                    <div class="row">
                        @foreach($posts as $post)
                            <div class="col-md-6 mb-4">
                                <article class="blog-post-card h-100">
                                    @if($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}" class="blog-post-image" alt="{{ $post->title }}">
                                    @endif
                                    
                                    <div class="blog-post-content">
                                        <h2 class="blog-post-title" style="font-size: 1.25rem;">
                                            <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $post->slug]) }}">
                                                {{ $post->title }}
                                            </a>
                                        </h2>
                                        
                                        <div class="blog-post-excerpt small">
                                            {{ Str::limit(strip_tags($post->content), 100) }}
                                        </div>
                                        
                                        <div class="blog-post-meta mt-2">
                                            <div class="blog-post-date">
                                                <i class="fas fa-calendar"></i>
                                                {{ $post->published_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay posts en esta categoría.
                    </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Blog
                    </a>
                </div>
            </div>

            <div class="blog-sidebar">
                @include('blog.partials.sidebar')
            </div>
        </div>
    </div>
@endsection