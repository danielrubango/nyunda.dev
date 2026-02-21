<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListLinksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locale' => [
                'nullable',
                'string',
                Rule::in([
                    'all',
                    ...config('app.supported_locales', ['fr', 'en']),
                ]),
            ],
            'q' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function localeSelection(): string
    {
        return (string) $this->validated('locale', 'all');
    }

    public function localeFilter(): ?string
    {
        $locale = $this->localeSelection();

        return $locale === 'all' ? null : $locale;
    }

    public function searchTerm(): ?string
    {
        $search = $this->validated('q');

        if (! is_string($search)) {
            return null;
        }

        $normalizedSearch = trim($search);

        return $normalizedSearch === '' ? null : $normalizedSearch;
    }

    public function resolvedUserLocale(): string
    {
        $supportedLocales = config('app.supported_locales', ['fr', 'en']);
        $appLocale = app()->getLocale();

        if (is_string($appLocale) && in_array($appLocale, $supportedLocales, true)) {
            return $appLocale;
        }

        return $this->getPreferredLanguage($supportedLocales)
            ?? (string) config('app.locale', 'fr');
    }
}
