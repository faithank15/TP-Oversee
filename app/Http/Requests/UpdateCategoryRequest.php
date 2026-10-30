<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('categories')->where('user_id', $this->user()->id)->ignore($this->category),
            ],
            'prefix' => [
                'required', 'alpha', 'min:2', 'max:5',
                Rule::unique('categories')->where('user_id', $this->user()->id)->ignore($this->category),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'prefix' => $this->prefix ? Str::upper(Str::ascii(trim($this->prefix))) : null,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'prefix' => 'préfixe',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Vous avez déjà une catégorie avec ce nom.',
            'prefix.unique' => 'Ce préfixe est déjà utilisé par une autre catégorie.',
        ];
    }
}
