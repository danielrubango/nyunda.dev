<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class StoreForumReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body_markdown' => ['required', 'string', 'min:2', 'max:10000'],
        ];
    }

    public function bodyMarkdown(): string
    {
        return (string) $this->validated('body_markdown');
    }
}
