<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()
                ->with('permissions')
                ->orderBy('name')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')->where('guard_name', 'web'),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions(
            Permission::query()->whereIn('id', $validated['permissions'] ?? [])->get()
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => Permission::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'name')
                    ->ignore($role->id)
                    ->where('guard_name', 'web'),
            ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', Rule::exists('permissions', 'id')],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions(
            Permission::query()->whereIn('id', $validated['permissions'] ?? [])->get()
        );

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role updated successfully.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name === 'admin') {
            return redirect()
                ->route('admin.roles.index')
                ->withErrors(['role' => 'The admin role cannot be deleted.']);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('status', 'Role deleted successfully.');
    }
}
