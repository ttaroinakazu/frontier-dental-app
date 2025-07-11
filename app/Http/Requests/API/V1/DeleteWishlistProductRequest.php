<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Foundation\Http\FormRequest;

class DeleteWishlistProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product' => ['required', 'integer', 'exists:products,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'product' => $this->route('product'),
        ]);
    }

    public function messages(): array
    {
        return [
            'product.required' => 'A product ID is required',
            'product.integer' => 'The product ID must be an integer',
            'product.exists' => 'The selected product does not exist',
        ];
    }
}
