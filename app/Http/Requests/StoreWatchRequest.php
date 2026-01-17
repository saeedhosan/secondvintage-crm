<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Store Watch Request - Handles validation for watch creation
 * 
 * Validates and authorizes incoming requests for creating new watches
 * in the inventory management system.
 */
class StoreWatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:watches,sku'],
            'status' => ['nullable', 'string', 'max:255'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'case_size' => ['nullable', 'string', 'max:255'],
            'wrist_size' => ['nullable', 'string', 'max:255'],
            'caliber' => ['nullable', 'string', 'max:255'],
            'timegrapher' => ['nullable', 'string', 'max:255'],
            'cost_original' => ['nullable', 'numeric'],
            'cost_euro' => ['nullable', 'numeric'],
            'location' => ['nullable', 'string', 'max:255'],
            'batch' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cost_currency' => ['nullable', 'string', 'max:3'],
            'cost_currency_rate' => ['nullable', 'numeric'],
            'cost_currency_rate_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'ai_instructions' => ['nullable', 'string'],
            'ai_thread_id' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:100'],
            'images.*.id' => ['nullable'],
            'images.*.file' => ['nullable', 'file', 'image', 'max:5120'],
            'images.*.useForAI' => ['nullable', 'bool'],
            'images_empty' => ['nullable', 'string', 'in:true,false'],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The watch name is required.',
            'brand.required' => 'The brand name is required.',
            'sku.unique' => 'This SKU is already taken.',
            'images.max' => 'You cannot upload more than 100 images.',
            'images.*.file.image' => 'Each file must be an image.',
            'images.*.file.max' => 'Each image may not be larger than 5MB.',
        ];
    }
}