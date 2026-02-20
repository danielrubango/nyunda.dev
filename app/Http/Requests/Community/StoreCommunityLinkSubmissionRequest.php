<?php

namespace App\Http\Requests\Community;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommunityLinkSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'locale' => [
                'required',
                'string',
                Rule::in(config('app.supported_locales', ['fr', 'en'])),
            ],
            'title' => ['nullable', 'string', 'min:5', 'max:255'],
            'excerpt' => ['nullable', 'string', 'min:10', 'max:1000'],
            'external_url' => ['required', 'url:http,https', 'max:2048'],
            'external_description' => ['nullable', 'string', 'max:2000'],
            'external_site_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'locale.required' => 'La langue est obligatoire.',
            'locale.in' => 'La langue selectionnee est invalide.',
            'title.min' => 'Le titre doit contenir au moins 5 caracteres.',
            'excerpt.min' => 'Le resume doit contenir au moins 10 caracteres.',
            'external_url.required' => "L'URL externe est obligatoire.",
            'external_url.url' => "L'URL externe doit etre une URL valide.",
        ];
    }

    /**
     * @return array{
     *     locale: string,
     *     title: ?string,
     *     excerpt: ?string,
     *     external_url: string,
     *     external_description: ?string,
     *     external_site_name: ?string
     * }
     */
    public function submissionData(): array
    {
        return [
            'locale' => (string) $this->validated('locale'),
            'title' => $this->stringOrNull('title'),
            'excerpt' => $this->stringOrNull('excerpt'),
            'external_url' => (string) $this->validated('external_url'),
            'external_description' => $this->stringOrNull('external_description'),
            'external_site_name' => $this->stringOrNull('external_site_name'),
        ];
    }

    protected function stringOrNull(string $key): ?string
    {
        $value = $this->validated($key);

        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
