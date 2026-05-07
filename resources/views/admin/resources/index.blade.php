<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Resource Management
            </h2>
            <a href="{{ route('admin.resources.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-500">
                Add Resource
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-lg p-4 mb-4">
                <form method="GET" action="{{ route('admin.resources.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input
                        type="text"
                        name="q"
                        value="{{ $filters['q'] }}"
                        placeholder="Search..."
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
                    <select name="visibility" class="rounded-md border-gray-300 text-sm">
                        <option value="">All Visibility</option>
                        <option value="public" @selected($filters['visibility'] === 'public')>Public</option>
                        <option value="private" @selected($filters['visibility'] === 'private')>Private</option>
                    </select>
                    <button class="inline-flex justify-center items-center px-4 py-2 bg-gray-900 text-white text-sm rounded-md hover:bg-black">
                        Filter
                    </button>
                </form>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Title</th>
                                <th class="px-4 py-3 text-left">Type</th>
                                <th class="px-4 py-3 text-left">Visibility</th>
                                <th class="px-4 py-3 text-left">Source</th>
                                <th class="px-4 py-3 text-left">Updated</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($resources as $resource)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-900">{{ $resource->title }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $resource->uploader?->name ?? 'System' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ ucfirst($resource->resource_type) }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ ucfirst($resource->visibility) }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ ucfirst($resource->source_type) }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $resource->updated_at->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ route('library.download', $resource) }}" class="text-indigo-600 hover:text-indigo-500">Open</a>
                                            <a href="{{ route('admin.resources.edit', $resource) }}" class="text-gray-700 hover:text-gray-900">Edit</a>
                                            <form method="POST" action="{{ route('admin.resources.destroy', $resource) }}" onsubmit="return confirm('Delete this resource?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-500">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No resources found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $resources->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
