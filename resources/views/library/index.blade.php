<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'IT Store') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 min-h-screen">
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('library.index') }}" class="text-lg font-semibold text-gray-900">
                    {{ __('resources.library_title') }}
                </a>

                <div class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">{{ __('resources.dashboard') }}</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-gray-900">{{ __('resources.logout') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">{{ __('resources.login') }}</a>
                        @if (config('settings.registration_enabled', true))
                            <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900">{{ __('resources.register') }}</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                <aside class="md:col-span-4 lg:col-span-3">
                    <div class="md:sticky md:top-6 bg-white border border-gray-200 rounded-lg p-5 space-y-4">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ __('resources.heading') }}</h1>
                            <p class="text-sm text-gray-600 mt-1">{{ __('resources.subheading') }}</p>
                        </div>

                        <form method="GET" action="{{ route('library.index') }}" class="space-y-3">
                            <input
                                type="text"
                                name="q"
                                value="{{ $filters['q'] }}"
                                placeholder="{{ __('resources.search_placeholder') }}"
                                class="w-full rounded-md border-gray-300 text-sm"
                            />

                            <select name="resource_type" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">{{ __('resources.all_types') }}</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" @selected($filters['resource_type'] === $type)>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="category_id" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">{{ __('resources.all_categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected($filters['category_id'] === (string) $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <select name="tag_id" class="w-full rounded-md border-gray-300 text-sm">
                                <option value="">{{ __('resources.all_tags') }}</option>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" @selected($filters['tag_id'] === (string) $tag->id)>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>

                            @if ($canViewPrivate)
                                <select name="visibility" class="w-full rounded-md border-gray-300 text-sm">
                                    <option value="">{{ __('resources.public_private') }}</option>
                                    <option value="public" @selected($filters['visibility'] === 'public')>{{ __('resources.public') }}</option>
                                    <option value="private" @selected($filters['visibility'] === 'private')>{{ __('resources.private') }}</option>
                                </select>
                            @endif

                            <div class="flex gap-2">
                                <button class="inline-flex justify-center items-center px-4 py-2 bg-gray-900 text-white text-sm rounded-md hover:bg-black">
                                    {{ __('resources.filter') }}
                                </button>
                                <a href="{{ route('library.index') }}" class="inline-flex justify-center items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm rounded-md hover:bg-gray-300">
                                    {{ __('resources.reset') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </aside>

                <section class="md:col-span-8 lg:col-span-9">
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-5">
                            <h3 class="font-semibold text-gray-900">{{ __('resources.latest_resources') }}</h3>
                            <ul class="mt-3 space-y-2 text-sm text-gray-700">
                                @forelse ($latestResources as $latest)
                                    <li class="flex justify-between"><span>{{ $latest->title }}</span><span class="text-gray-500">{{ $latest->updated_at->diffForHumans() }}</span></li>
                                @empty
                                    <li class="text-gray-500">{{ __('resources.none_found') }}</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="bg-white border border-gray-200 rounded-lg p-5">
                            <h3 class="font-semibold text-gray-900">{{ __('resources.most_downloaded') }}</h3>
                            <ul class="mt-3 space-y-2 text-sm text-gray-700">
                                @forelse ($mostDownloadedResources as $top)
                                    <li class="flex justify-between"><span>{{ $top->title }}</span><span class="text-gray-500">{{ trans_choice(':count download|:count downloads', $top->download_count, ['count' => $top->download_count]) }}</span></li>
                                @empty
                                    <li class="text-gray-500">{{ __('resources.none_found') }}</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse ($resources as $resource)
                    <article class="bg-white border border-gray-200 rounded-lg p-5 flex flex-col">
                        <div class="flex items-center justify-between gap-2">
                            <h2 class="font-semibold text-gray-900 text-lg">{{ $resource->title }}</h2>
                            <span class="text-xs px-2 py-1 rounded-full {{ $resource->visibility === 'private' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ ucfirst($resource->visibility) }}
                            </span>
                        </div>

                        <div class="mt-2 text-xs text-gray-500">
                            {{ strtoupper($resource->resource_type) }} • {{ strtoupper($resource->source_type) }}
                            @if ($resource->version)
                                • v{{ $resource->version }}
                            @endif
                        </div>

                        @if (! blank($resource->description))
                            <p class="mt-3 text-sm text-gray-700 flex-grow">{{ $resource->description }}</p>
                        @else
                            <p class="mt-3 text-sm text-gray-500 flex-grow">{{ __('resources.no_description') }}</p>
                        @endif

                        @if ($resource->tags->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($resource->tags as $tag)
                                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500">
                                {{ __('resources.updated') }} {{ $resource->updated_at->diffForHumans() }}
                                @if ($resource->uploader)
                                    • {{ __('resources.by') }} {{ $resource->uploader->name }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ trans_choice(':count download|:count downloads', $resource->download_count, ['count' => $resource->download_count]) }}
                            </p>
                            @if ($resource->source_type === 'link')
                                <a
                                    href="{{ route('library.download', $resource) }}"
                                    class="mt-3 inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-500"
                                >
                                    {{ __('resources.open_link') }}
                                </a>
                            @else
                                @php($resourceFiles = $resource->files)
                                @if ($resourceFiles->count() <= 1)
                                    <a
                                        href="{{ route('library.download', $resource) }}"
                                        class="mt-3 inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-500"
                                    >
                                        {{ __('resources.download') }}
                                    </a>
                                @else
                                    <div class="mt-3 space-y-2">
                                        <a
                                            href="{{ route('library.files.download-all', $resource) }}"
                                            class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-500"
                                        >
                                            {{ __('resources.download') }} (ZIP)
                                        </a>
                                        <div class="space-y-1">
                                            @foreach ($resourceFiles as $resourceFile)
                                                <a
                                                    href="{{ route('library.files.download', [$resource, $resourceFile]) }}"
                                                    class="block text-sm text-indigo-600 hover:text-indigo-500 truncate"
                                                    title="{{ $resourceFile->file_name }}"
                                                >
                                                    {{ __('resources.download') }}: {{ $resourceFile->file_name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full bg-white border border-gray-200 rounded-lg p-8 text-center text-gray-500">
                        {{ __('resources.none_for_filters') }}
                    </div>
                @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $resources->links() }}
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
