<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'name' => 'required|min:5|max:255',
            'description' => 'nullable',
            'image' => 'image|max:10240', // max 10mb
            'price' => 'required|numeric|decimal:2',
            'discount_percentage' => 'nullable|numeric|decimal:2',
        ];
    }
}
