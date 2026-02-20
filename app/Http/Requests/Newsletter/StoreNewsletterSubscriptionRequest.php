<?php

namespace App\Http\Requests\Newsletter;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewsletterSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'locale' => [
                'nullable',
                'string',
                Rule::in(config('app.supported_locales', ['fr', 'en'])),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => "L'adresse email est obligatoire.",
            'email.email' => "L'adresse email n'est pas valide.",
            'locale.in' => 'La langue selectionnee est invalide.',
        ];
    }

    public function email(): string
    {
        return mb_strtolower(trim((string) $this->validated('email')));
    }

    public function locale(): string
    {
        $locale = $this->validated('locale');

        if (is_string($locale) && $locale !== '') {
            return $locale;
        }

        return $this->getPreferredLanguage(config('app.supported_locales', ['fr', 'en']))
            ?? (string) config('app.locale', 'fr');
    }
}
