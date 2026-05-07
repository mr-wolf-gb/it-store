<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Role Management
            </h2>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-500">
                Add Role
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

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">Role</th>
                                <th class="px-4 py-3 text-left">Permissions</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($roles as $role)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $role->name }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse ($role->permissions as $permission)
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                                    {{ $permission->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500">No permissions</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-500">Edit</a>
                                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-500">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">No roles found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
