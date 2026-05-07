<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ]);

        $roleNames = Role::query()
            ->whereIn('id', $validated['roles'] ?? [])
            ->pluck('name')
            ->all();

        $user->syncRoles($roleNames);

        return redirect()
            ->route('admin.user-roles.index')
            ->with('status', "Roles updated for {$user->name}.");
    }
}
