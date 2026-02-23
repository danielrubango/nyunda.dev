<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'body_markdown' => ['required', 'string', 'min:2', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'body_markdown.required' => __('ui.blog.comments.validation_required'),
            'body_markdown.min' => __('ui.blog.comments.validation_min'),
            'body_markdown.max' => __('ui.blog.comments.validation_max'),
        ];
    }

    public function bodyMarkdown(): string
    {
        return (string) $this->validated('body_markdown');
    }
}
