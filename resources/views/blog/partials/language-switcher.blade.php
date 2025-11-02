<div class="language-switcher">
    @php
        $currentRoute = Route::currentRouteName();
        $currentParams = Route::current()->parameters();
        $locales = [
            'en' => ['name' => 'English', 'flag' => '🇺🇸'],
            'es' => ['name' => 'Español', 'flag' => '🇪🇸'],
            'pt' => ['name' => 'Português', 'flag' => '🇧🇷']
        ];
    @endphp
    
    <div class="relative inline-block text-left">
        <div>
            <button type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" id="language-menu-button" aria-expanded="true" aria-haspopup="true">
                <span class="mr-2">{{ $locales[app()->getLocale()]['flag'] }}</span>
                {{ $locales[app()->getLocale()]['name'] }}
                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div class="hidden origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-within z-50" role="menu" aria-orientation="vertical" aria-labelledby="language-menu-button" tabindex="-1" id="language-menu">
            <div class="py-1" role="none">
                @foreach($locales as $locale => $data)
                    @if($locale !== app()->getLocale())
                        <a href="{{ route($currentRoute, array_merge($currentParams, ['locale' => $locale])) }}" 
                           class="text-gray-700 block px-4 py-2 text-sm hover:bg-gray-100" 
                           role="menuitem" 
                           tabindex="-1">
                            <span class="mr-2">{{ $data['flag'] }}</span>
                            {{ $data['name'] }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('language-menu-button');
            const menu = document.getElementById('language-menu');
            
            button.addEventListener('click', function() {
                menu.classList.toggle('hidden');
            });
            
            document.addEventListener('click', function(event) {
                if (!button.contains(event.target) && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                }
            });
        });
    </script>
</div>
