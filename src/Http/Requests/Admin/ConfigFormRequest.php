<?php

namespace Rvsitebuilder\Larecipe\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConfigFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'github' => 'required',

            // 'forum.services.disqus.site_name' => 'required',

            'versions.default' => ['required', 'not_regex:/(\,)|(\,)/'],

            'versions.published' => ['required', 'not_regex:/(^\,)(.*?)|(.*?)(\,$)/'],

            'languages.default' => ['required', 'regex:/(^[a-zA-Z_-]+)$/'],

            'languages.published' => ['required', 'regex:/^[a-zA-Z_-]+(\,[a-zA-Z_-]+)+|^[a-zA-Z_-]+$/'],
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
