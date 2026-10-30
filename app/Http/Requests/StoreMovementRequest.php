<?php

namespace App\Http\Requests;

use App\Enums\MovementType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMovementRequest extends FormRequest
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
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->where('user_id', $this->user()->id)->whereNull('deleted_at'),
            ],
            'type' => ['required', Rule::enum(MovementType::class)],
            'quantity' => [
                'required', 'integer',
                $this->input('type') === MovementType::Ajustement->value ? 'min:0' : 'min:1',
            ],
            'note' => ['nullable', 'string', 'max:255', 'required_if:type,'.MovementType::Ajustement->value],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'produit',
            'type' => 'type de mouvement',
            'quantity' => 'quantité',
            'note' => 'motif',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.exists' => 'Choisissez un produit de la liste.',
            'note.required_if' => 'Le motif est obligatoire pour un ajustement.',
        ];
    }
}
