<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class UpdateForumReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_hidden' => ['required', 'boolean'],
        ];
    }

    public function isHidden(): bool
    {
        return (bool) $this->boolean('is_hidden');
    }
}
