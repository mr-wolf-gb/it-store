<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function index(): View
    {
        return view('admin.user-roles.index', [
            'users' => User::query()
                ->with('roles')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.user-roles.edit', [
            'managedUser' => $user->load('roles'),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.user-roles.create', [
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $roleNames = Role::query()
            ->whereIn('id', $validated['roles'] ?? [])
            ->pluck('name')
            ->all();

        $user->syncRoles($roleNames);

        return redirect()
            ->route('admin.user-roles.index')
            ->with('status', __('User created successfully.'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $roleNames = Role::query()
            ->whereIn('id', $validated['roles'] ?? [])
            ->pluck('name')
            ->all();

        $user->syncRoles($roleNames);

        if (! empty($validated['password'])) {
            $user->forceFill([
                'password' => $validated['password'],
            ])->save();
        }

        return redirect()
            ->route('admin.user-roles.index')
            ->with('status', "User access updated for {$user->name}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ((int) $request->user()->id === (int) $user->id) {
            return back()->withErrors([
                'delete_user' => __('You cannot delete your own account from user management.'),
            ]);
        }

        $user->delete();

        return redirect()
            ->route('admin.user-roles.index')
            ->with('status', __('User deleted successfully.'));
    }
}
