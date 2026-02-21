<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateLocaleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => [
                'required',
                'string',
                Rule::in(config('app.supported_locales', ['fr', 'en'])),
            ],
        ]);

        $locale = (string) $validated['locale'];

        $request->session()->put('preferred_locale', $locale);

        if ($request->user() !== null) {
            $request->user()->forceFill([
                'preferred_locale' => $locale,
            ])->save();
        }

        return redirect()->back();
    }
}
