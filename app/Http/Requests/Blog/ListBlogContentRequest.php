<?php

namespace App\Http\Requests\Blog;

use App\Enums\ContentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListBlogContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
            'type' => [
                'nullable',
                'string',
                Rule::in(array_map(
                    fn (ContentType $type): string => $type->value,
                    ContentType::cases(),
                )),
            ],
            'tag' => [
                'nullable',
                'string',
                Rule::exists('tags', 'slug'),
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

    public function typeFilter(): ?string
    {
        $type = $this->validated('type');

        return $type === null ? null : (string) $type;
    }

    public function tagFilter(): ?string
    {
        $tag = $this->validated('tag');

        return $tag === null ? null : (string) $tag;
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
