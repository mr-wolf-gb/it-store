<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('IT Support Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">Total Resources</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['resources_total'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">Public</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['resources_public'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">Private</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['resources_private'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">Roles</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['roles_total'] }}</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-500">Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['users_total'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Latest Resource Updates</h3>
                    </div>
                    <div class="p-6">
                        @if ($recentResources->isEmpty())
                            <p class="text-sm text-gray-500">No resources yet.</p>
                        @else
                            <ul class="space-y-4">
                                @foreach ($recentResources as $resource)
                                    <li class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $resource->title }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ ucfirst($resource->resource_type) }} • {{ ucfirst($resource->visibility) }}
                                            </p>
                                        </div>
                                        <a
                                            href="{{ route('library.download', $resource) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-900 text-white text-xs rounded-md hover:bg-black"
                                        >
                                            Open
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('library.index') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                            Browse resource library
                        </a>
                        @can('resources.manage')
                            <a href="{{ route('admin.resources.create') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                Add new resource
                            </a>
                        @endcan
                        @can('roles.manage')
                            <a href="{{ route('admin.roles.index') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                Manage roles and permissions
                            </a>
                        @endcan
                        @can('users.manage_roles')
                            <a href="{{ route('admin.user-roles.index') }}" class="block text-sm text-indigo-600 hover:text-indigo-500">
                                Assign roles to users
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
