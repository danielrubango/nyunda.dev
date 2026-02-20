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

    public function resolvedUserLocale(): string
    {
        $userLocale = $this->user()?->preferred_locale;

        if (is_string($userLocale) && $userLocale !== '') {
            return $userLocale;
        }

        return $this->getPreferredLanguage(config('app.supported_locales', ['fr', 'en']))
            ?? (string) config('app.locale', 'fr');
    }
}
