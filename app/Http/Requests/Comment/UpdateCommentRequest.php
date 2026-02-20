<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
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
