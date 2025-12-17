<!-- Search -->
    <div class="sidebar-section">
        <h3 class="sidebar-title"><i class="fas fa-search"></i> {{ __('blog.search') }}</h3>
        <form method="GET" action="{{ route('blog.index', ['locale' => app()->getLocale()]) }}">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="{{ __('blog.search_placeholder') }}" value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Categories -->
    @if(isset($categories) && $categories->count() > 0)
        <div class="sidebar-section">
            <h3 class="sidebar-title"><i class="fas fa-folder"></i> {{ __('blog.categories') }}</h3>
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
    @if(isset($tags) && $tags->count() > 0)
        <div class="sidebar-section">
            <h3 class="sidebar-title"><i class="fas fa-tags"></i> {{ __('blog.tags') }}</h3>
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
    @if(isset($recentPosts) && $recentPosts->count() > 0)
        <div class="sidebar-section">
            <h3 class="sidebar-title"><i class="fas fa-clock"></i> {{ __('blog.recent_posts') }}</h3>
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
