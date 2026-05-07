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
                    IT Support Library
                </a>

                <div class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-gray-700 hover:text-gray-900">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                        <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900">Register</a>
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

            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Apps, Scripts, and Documents</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Public resources are visible to everyone. Private resources are shown when your account has private-access permission.
                </p>

                <form method="GET" action="{{ route('library.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-5">
                    <input
                        type="text"
                        name="q"
                        value="{{ $filters['q'] }}"
                        placeholder="Search title or description..."
                        class="rounded-md border-gray-300 text-sm"
                    />

                    <select name="resource_type" class="rounded-md border-gray-300 text-sm">
                        <option value="">All Types</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected($filters['resource_type'] === $type)>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>

                    @if ($canViewPrivate)
                        <select name="visibility" class="rounded-md border-gray-300 text-sm">
                            <option value="">Public + Private</option>
                            <option value="public" @selected($filters['visibility'] === 'public')>Public</option>
                            <option value="private" @selected($filters['visibility'] === 'private')>Private</option>
                        </select>
                    @endif

                    <button class="inline-flex justify-center items-center px-4 py-2 bg-gray-900 text-white text-sm rounded-md hover:bg-black">
                        Filter
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
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
                        </div>

                        @if (! blank($resource->description))
                            <p class="mt-3 text-sm text-gray-700 flex-grow">{{ $resource->description }}</p>
                        @else
                            <p class="mt-3 text-sm text-gray-500 flex-grow">No description provided.</p>
                        @endif

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500">
                                Updated {{ $resource->updated_at->format('Y-m-d H:i') }}
                                @if ($resource->uploader)
                                    • by {{ $resource->uploader->name }}
                                @endif
                            </p>
                            <a
                                href="{{ route('library.download', $resource) }}"
                                class="mt-3 inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-500"
                            >
                                {{ $resource->source_type === 'upload' ? 'Download' : 'Open Link' }}
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full bg-white border border-gray-200 rounded-lg p-8 text-center text-gray-500">
                        No resources found for the current filters.
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $resources->links() }}
            </div>
        </main>
    </body>
</html>
