<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\ContentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDashboardContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ContentType::class)],
            'locale' => [
                'required',
                'string',
                Rule::in(config('app.supported_locales', ['fr', 'en'])),
            ],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'body_markdown' => ['nullable', 'string', 'required_if:type,'.ContentType::InternalPost->value],
            'external_url' => ['nullable', 'url:http,https', 'max:2048', 'required_unless:type,'.ContentType::InternalPost->value],
            'external_description' => ['nullable', 'string', 'max:2000'],
            'external_site_name' => ['nullable', 'string', 'max:255'],
            'featured_image_url' => ['nullable', 'url:http,https', 'max:2048'],
        ];
    }

    /**
     * @return array{
     *     type: ContentType,
     *     locale: string,
     *     title: string,
     *     excerpt: ?string,
     *     body_markdown: ?string,
     *     external_url: ?string,
     *     external_description: ?string,
     *     external_site_name: ?string,
     *     featured_image_url: ?string
     * }
     */
    public function submissionData(): array
    {
        return [
            'type' => ContentType::from((string) $this->validated('type')),
            'locale' => (string) $this->validated('locale'),
            'title' => trim((string) $this->validated('title')),
            'excerpt' => $this->stringOrNull('excerpt'),
            'body_markdown' => $this->stringOrNull('body_markdown'),
            'external_url' => $this->stringOrNull('external_url'),
            'external_description' => $this->stringOrNull('external_description'),
            'external_site_name' => $this->stringOrNull('external_site_name'),
            'featured_image_url' => $this->stringOrNull('featured_image_url'),
        ];
    }

    protected function stringOrNull(string $key): ?string
    {
        $value = $this->validated($key);

        if ($value === null) {
            return null;
        }

        $stringValue = trim((string) $value);

        return $stringValue === '' ? null : $stringValue;
    }
}
