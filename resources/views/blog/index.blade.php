@extends('layouts.blog')

@section('title', __('blog.title') . ' - José Rafael Camejo')

@section('content')
<div class="blog-container">
    <div class="blog-grid">
        <div class="blog-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>{{ __('blog.title') }}</h1>
                
                @if(request('category') || request('tag') || request('search'))
                    <div>
                        <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i> {{ __('blog.clear_filters') }}
                        </a>
                    </div>
                @endif
            </div>

            @if(request('category'))
                <div class="alert alert-info">
                    <i class="fas fa-folder"></i> {{ __('blog.showing_category') }} <strong>{{ request('category') }}</strong>
                </div>
            @endif

            @if(request('tag'))
                <div class="alert alert-info">
                    <i class="fas fa-tag"></i> {{ __('blog.showing_tag') }} <strong>{{ request('tag') }}</strong>
                </div>
            @endif

            @if(request('search'))
                <div class="alert alert-info">
                    <i class="fas fa-search"></i> {{ __('blog.search_results') }} <strong>{{ request('search') }}</strong>
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
                    <h4>{{ __('blog.no_posts_found') }}</h4>
                    <p class="text-muted">
                        @if(request('search') || request('category') || request('tag'))
                            {{ __('blog.adjust_filters') }}
                        @else
                            {{ __('blog.no_posts_published') }}
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <div class="blog-sidebar">
            @include('blog.partials.sidebar')
        </div>
    </div>
</div>
@endsection