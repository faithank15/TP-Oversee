<?php

namespace App\Http\Requests;

use App\Enums\Unit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', Rule::exists('categories', 'id')->where('user_id', $this->user()->id)],
            'reference' => [
                'nullable', 'string', 'max:30', 'alpha_dash',
                Rule::unique('products')->where('user_id', $this->user()->id),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'unit' => ['required', Rule::enum(Unit::class)],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'alert_threshold' => ['required', 'integer', 'min:0'],
            'initial_quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'reference' => $this->reference ? Str::upper(trim($this->reference)) : null,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'category_id' => 'catégorie',
            'reference' => 'référence',
            'unit' => 'unité',
            'price' => 'prix',
            'alert_threshold' => "seuil d'alerte",
            'initial_quantity' => 'quantité initiale',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'reference.unique' => 'Cette référence est déjà utilisée par un autre produit.',
            'category_id.exists' => 'Choisissez une catégorie de la liste.',
        ];
    }
}
