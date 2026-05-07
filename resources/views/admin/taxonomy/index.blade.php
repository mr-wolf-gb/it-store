<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('taxonomy.title') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('taxonomy.categories') }}</h3>
                <form method="POST" action="{{ route('admin.taxonomy.categories.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <x-text-input name="name" class="w-full" :value="old('name')" :placeholder="__('taxonomy.category_name')" />
                    <textarea name="description" class="w-full rounded-md border-gray-300" rows="3" placeholder="{{ __('taxonomy.description') }}">{{ old('description') }}</textarea>
                    <x-primary-button>{{ __('taxonomy.add_category') }}</x-primary-button>
                </form>
                <ul class="mt-6 space-y-2 text-sm">
                    @foreach ($categories as $category)
                        <li class="flex justify-between items-center border-b pb-2">
                            <span>{{ $category->name }}</span>
                            <form method="POST" action="{{ route('admin.taxonomy.categories.destroy', $category) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">{{ __('taxonomy.delete') }}</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4">{{ $categories->links() }}</div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('taxonomy.tags') }}</h3>
                <form method="POST" action="{{ route('admin.taxonomy.tags.store') }}" class="mt-4 flex gap-3">
                    @csrf
                    <x-text-input name="name" class="w-full" :value="old('name')" :placeholder="__('taxonomy.tag_name')" />
                    <x-primary-button>{{ __('taxonomy.add') }}</x-primary-button>
                </form>
                <ul class="mt-6 space-y-2 text-sm">
                    @foreach ($tags as $tag)
                        <li class="flex justify-between items-center border-b pb-2">
                            <span>#{{ $tag->name }}</span>
                            <form method="POST" action="{{ route('admin.taxonomy.tags.destroy', $tag) }}">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">{{ __('taxonomy.delete') }}</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-4">{{ $tags->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
