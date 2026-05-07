<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('dashboard.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @php
                $storageBytes = (int) $stats['storage_bytes'];
                $storageReadable = $storageBytes >= 1048576
                    ? number_format($storageBytes / 1048576, 2).' MB'
                    : number_format($storageBytes / 1024, 2).' KB';
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.total_resources') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['resources_total'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.public') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['resources_public'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.private') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['resources_private'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.status_mix') }}</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $stats['resources_draft'] }} / {{ $stats['resources_published'] }} / {{ $stats['resources_archived'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.roles') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['roles_total'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.users') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['users_total'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.featured') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['resources_featured'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.total_downloads') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_downloads'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.attached_files') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['files_total'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">{{ __('dashboard.storage_used') }}</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $storageReadable }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('dashboard.latest_updates') }}</h3>
                    </div>
                    <div class="p-6">
                        @if ($recentResources->isEmpty())
                            <p class="text-sm text-gray-500">{{ __('dashboard.no_resources') }}</p>
                        @else
                            <ul class="space-y-4">
                                @foreach ($recentResources as $resource)
                                    <li class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 border-b border-gray-100 pb-3">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $resource->title }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ ucfirst($resource->resource_type) }} • {{ ucfirst($resource->visibility) }} • {{ ucfirst($resource->status) }}
                                                @if ($resource->category)
                                                    • {{ $resource->category->name }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ __('dashboard.files_count', ['count' => $resource->files->count()]) }} • {{ trans_choice(':count download|:count downloads', $resource->download_count, ['count' => $resource->download_count]) }}
                                            </p>
                                        </div>
                                        <div class="flex flex-col items-start gap-1">
                                            @if ($resource->source_type === 'link')
                                                <a
                                                    href="{{ route('library.download', $resource) }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-gray-900 text-white text-xs rounded-md hover:bg-black"
                                                >
                                                    {{ __('dashboard.open_link') }}
                                                </a>
                                            @elseif ($resource->files->count() > 1)
                                                <a
                                                    href="{{ route('library.files.download-all', $resource) }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-gray-900 text-white text-xs rounded-md hover:bg-black"
                                                >
                                                    {{ __('dashboard.download_all') }}
                                                </a>
                                                <div class="space-y-1">
                                                    @foreach ($resource->files->take(3) as $resourceFile)
                                                        <a href="{{ route('library.files.download', [$resource, $resourceFile]) }}" class="block text-xs text-indigo-600 hover:text-indigo-500">
                                                            {{ $resourceFile->file_name }}
                                                        </a>
                                                    @endforeach
                                                    @if ($resource->files->count() > 3)
                                                        <p class="text-xs text-gray-500">{{ __('dashboard.more_files', ['count' => $resource->files->count() - 3]) }}</p>
                                                    @endif
                                                </div>
                                            @else
                                                <a
                                                    href="{{ route('library.download', $resource) }}"
                                                    class="inline-flex items-center px-3 py-1.5 bg-gray-900 text-white text-xs rounded-md hover:bg-black"
                                                >
                                                    {{ __('dashboard.download') }}
                                                </a>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('dashboard.top_categories') }}</h3>
                        </div>
                        <div class="p-6">
                            @if ($topCategories->isEmpty())
                                <p class="text-sm text-gray-500">{{ __('dashboard.no_categories') }}</p>
                            @else
                                <ul class="space-y-2 text-sm">
                                    @foreach ($topCategories as $category)
                                        <li class="flex justify-between">
                                            <span>{{ $category->name }}</span>
                                            <span class="text-gray-500">{{ $category->resources_count }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('dashboard.top_downloaded') }}</h3>
                        </div>
                        <div class="p-6">
                            @if ($topDownloadedResources->isEmpty())
                                <p class="text-sm text-gray-500">{{ __('dashboard.no_downloads') }}</p>
                            @else
                                <ul class="space-y-2 text-sm">
                                    @foreach ($topDownloadedResources as $topResource)
                                        <li class="flex justify-between">
                                            <span class="truncate pe-2">{{ $topResource->title }}</span>
                                            <span class="text-gray-500">{{ $topResource->download_count }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('dashboard.quick_actions') }}</h3>
                        </div>
                        <div class="p-6 space-y-3">
                            <a href="{{ route('library.index') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                {{ __('dashboard.browse_library') }}
                            </a>
                            @can('resources.manage')
                                <a href="{{ route('admin.resources.create') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ __('dashboard.add_resource') }}
                                </a>
                                <a href="{{ route('admin.resources.index') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ __('dashboard.review_resources') }}
                                </a>
                                <a href="{{ route('admin.taxonomy.index') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ __('dashboard.manage_taxonomy') }}
                                </a>
                            @endcan
                            @can('roles.manage')
                                <a href="{{ route('admin.roles.index') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ __('dashboard.manage_roles') }}
                                </a>
                            @endcan
                            @can('users.manage_roles')
                                <a href="{{ route('admin.user-roles.index') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                    {{ __('dashboard.assign_roles') }}
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
