<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCategory;
use App\Models\ResourceTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ResourceTaxonomyController extends Controller
{
    public function index(): View
    {
        return view('admin.taxonomy.index', [
            'categories' => ResourceCategory::query()->orderBy('name')->paginate(10, ['*'], 'categories_page'),
            'tags' => ResourceTag::query()->orderBy('name')->paginate(20, ['*'], 'tags_page'),
        ]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('resource_categories', 'name')],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        ResourceCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('status', __('Category added.'));
    }

    public function destroyCategory(ResourceCategory $category): RedirectResponse
    {
        $category->delete();

        return back()->with('status', __('Category deleted.'));
    }

    public function storeTag(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('resource_tags', 'name')],
        ]);

        ResourceTag::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        return back()->with('status', __('Tag added.'));
    }

    public function destroyTag(ResourceTag $tag): RedirectResponse
    {
        $tag->delete();

        return back()->with('status', __('Tag deleted.'));
    }
}
