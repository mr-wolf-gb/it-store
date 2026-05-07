<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Role Assignment
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3 text-left">User</th>
                                <th class="px-4 py-3 text-left">Email</th>
                                <th class="px-4 py-3 text-left">Roles</th>
                                <th class="px-4 py-3 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse ($user->roles as $role)
                                                <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                                    {{ $role->name }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-gray-500">No roles</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.user-roles.edit', $user) }}" class="text-indigo-600 hover:text-indigo-500">
                                            Edit Roles
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
