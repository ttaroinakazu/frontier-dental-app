<?php

namespace App\Http\Requests\API\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreWishlistRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'products' => ['sometimes', 'array'],
            'products.*.id' => ['required', 'integer', 'exists:products,id'],
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        // Get the original error messages
        $errors = $validator->errors()->messages();

        // Create a simplified error message
        $message = 'One or more products do not exist.';

        // Create a simplified errors array with just the invalid product IDs
        $invalidProducts = [];
        foreach ($errors as $key => $error) {
            if (preg_match('/products\.(\d+)\.id/', $key, $matches)) {
                $invalidProducts[] = $this->input('products')[$matches[1]]['id'];
            }
        }

        throw new HttpResponseException(response()->json([
            'message' => $message,
            'errors' => [
                'products' => ['The following product IDs do not exist: ' . implode(', ', $invalidProducts)]
            ]
        ], 422));
    }
}
