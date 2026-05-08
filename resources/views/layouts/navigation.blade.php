<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('library.index') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('library.index')" :active="request()->routeIs('library.*')">
                        {{ __('resources.library') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('resources.dashboard') }}
                    </x-nav-link>
                    @can('resources.manage')
                        <x-nav-link :href="route('admin.resources.index')" :active="request()->routeIs('admin.resources.*')">
                            {{ __('resources.manage_resources') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.taxonomy.index')" :active="request()->routeIs('admin.taxonomy.*')">
                            {{ __('resources.taxonomy') }}
                        </x-nav-link>
                    @endcan
                    @can('roles.manage')
                        <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                            {{ __('resources.roles') }}
                        </x-nav-link>
                    @endcan
                    @can('users.manage_roles')
                        <x-nav-link :href="route('admin.user-roles.index')" :active="request()->routeIs('admin.user-roles.*')">
                            {{ __('resources.user_roles') }}
                        </x-nav-link>
                    @endcan
                    @can('logs.view')
                        <x-nav-link :href="route('log-viewer.index')" :active="request()->routeIs('log-viewer.*')">
                            {{ __('Logs') }}
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <form method="POST" action="{{ route('locale.update') }}">
                    @csrf
                    <select name="locale" onchange="this.form.submit()" class="rounded-md border-gray-300 text-xs">
                        <option value="en" @selected(app()->getLocale() === 'en')>EN</option>
                        <option value="fr" @selected(app()->getLocale() === 'fr')>FR</option>
                    </select>
                </form>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('resources.profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('resources.log_out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('library.index')" :active="request()->routeIs('library.*')">
                {{ __('resources.library') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('resources.dashboard') }}
            </x-responsive-nav-link>
            @can('resources.manage')
                <x-responsive-nav-link :href="route('admin.resources.index')" :active="request()->routeIs('admin.resources.*')">
                    {{ __('resources.manage_resources') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.taxonomy.index')" :active="request()->routeIs('admin.taxonomy.*')">
                    {{ __('resources.taxonomy') }}
                </x-responsive-nav-link>
            @endcan
            @can('roles.manage')
                <x-responsive-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                    {{ __('resources.roles') }}
                </x-responsive-nav-link>
            @endcan
            @can('users.manage_roles')
                <x-responsive-nav-link :href="route('admin.user-roles.index')" :active="request()->routeIs('admin.user-roles.*')">
                    {{ __('resources.user_roles') }}
                </x-responsive-nav-link>
            @endcan
            @can('logs.view')
                <x-responsive-nav-link :href="route('log-viewer.index')" :active="request()->routeIs('log-viewer.*')">
                    {{ __('Logs') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <form method="POST" action="{{ route('locale.update') }}" class="px-4">
                    @csrf
                    <select name="locale" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 text-xs">
                        <option value="en" @selected(app()->getLocale() === 'en')>EN</option>
                        <option value="fr" @selected(app()->getLocale() === 'fr')>FR</option>
                    </select>
                </form>
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('resources.profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('resources.log_out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
