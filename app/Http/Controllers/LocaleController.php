<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    /**
     * @var list<string>
     */
    private const SUPPORTED_LOCALES = ['en', 'fr'];

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(self::SUPPORTED_LOCALES)],
        ]);

        $locale = $validated['locale'];
        $request->session()->put('locale', $locale);

        if ($request->user() !== null) {
            $request->user()->forceFill([
                'preferred_locale' => $locale,
            ])->save();
        }

        return back();
    }
}
