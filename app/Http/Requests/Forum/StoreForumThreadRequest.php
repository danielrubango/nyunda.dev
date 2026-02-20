<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class StoreForumThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locale' => ['required', 'string', 'in:'.implode(',', config('app.supported_locales', ['fr', 'en']))],
            'title' => ['required', 'string', 'min:5', 'max:160'],
            'body_markdown' => ['required', 'string', 'min:10', 'max:20000'],
        ];
    }

    /**
     * @return array{locale: string, title: string, body_markdown: string}
     */
    public function payload(): array
    {
        /** @var array{locale: string, title: string, body_markdown: string} $validated */
        $validated = $this->validated();

        return $validated;
    }
}
