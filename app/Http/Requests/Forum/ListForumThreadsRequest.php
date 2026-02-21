<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListForumThreadsRequest extends FormRequest
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
            'sort' => [
                'nullable',
                'string',
                Rule::in(['recent', 'active', 'replies']),
            ],
            'tag' => ['nullable', 'string', 'max:80'],
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

    public function sortFilter(): string
    {
        return (string) $this->validated('sort', 'recent');
    }

    public function requestedTag(): ?string
    {
        $tag = $this->validated('tag');

        if (! is_string($tag)) {
            return null;
        }

        $normalizedTag = trim($tag);

        return $normalizedTag === '' ? null : $normalizedTag;
    }
}
