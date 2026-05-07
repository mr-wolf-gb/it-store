<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Resource') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                @if ($errors->any())
                    <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                        <p class="font-semibold">{{ __('Please fix the errors below.') }}</p>
                        <ul class="mt-2 list-disc ps-5 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.resources.update', $supportResource) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.resources._form')

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                        <a href="{{ route('admin.resources.index') }}" class="text-sm text-gray-600 hover:text-gray-900">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
