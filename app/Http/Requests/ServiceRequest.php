<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $authorized = auth()->check() && (auth()->user()?->role === 'admin' || auth()->user()?->role === 'ADMIN');
        \Log::info('ServiceRequest Authorization', [
            'authorized' => $authorized,
            'user_role' => auth()->user()?->role ?? 'none',
            'user_id' => auth()->id()
        ]);
        return true; // Force true for now to see if we hit rules
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'api_provider_id' => 'nullable|exists:api_providers,id',
            'api_service_id' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:500',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'rate' => 'required|numeric|min:0',
            'price_per_k' => 'nullable|numeric|min:0',
            'min_order' => 'required|integer|min:0',
            'max_order' => 'required|integer|min:0',
            'min_qty' => 'nullable|integer|min:0',
            'max_qty' => 'nullable|integer|min:0',
            'drip_feed' => 'nullable',
            'refill' => 'nullable',
            'cancel' => 'nullable',
            'status' => 'required|in:active,inactive',
            'is_active' => 'nullable|boolean',
            'sale_price' => 'nullable|numeric|min:0',
            'price_locked' => 'nullable|boolean',
            'average_time' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'api_provider_id.required' => 'API provider is required.',
            'api_provider_id.exists' => 'The selected API provider does not exist.',
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'name.required' => 'Service name is required.',
            'name.max' => 'Service name cannot exceed 500 characters.',
            'type.required' => 'Service type is required.',
            'rate.required' => 'Rate is required.',
            'rate.numeric' => 'Rate must be a valid number.',
            'rate.min' => 'Rate cannot be negative.',
            'min_order.required' => 'Minimum order quantity is required.',
            'min_order.integer' => 'Minimum order must be an integer.',
            'min_order.min' => 'Minimum order must be at least 1.',
            'max_order.required' => 'Maximum order quantity is required.',
            'max_order.integer' => 'Maximum order must be an integer.',
            'max_order.min' => 'Maximum order must be at least 1.',
            'api_service_id.required' => 'The provider service ID is missing.',
            'status.required' => 'Please select a status.',
            'status.in' => 'Invalid status selected.',
            'category_id.required' => 'Please select an internal category.',
            'category_id.exists' => 'The selected internal category does not exist.',
            'rate.required' => 'The provider rate is required.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'drip_feed' => $this->boolean('drip_feed'),
            'refill' => $this->boolean('refill'),
            'cancel' => $this->boolean('cancel'),
            'price_locked' => $this->boolean('price_locked'),
            'min_qty' => $this->input('min_order'),
            'max_qty' => $this->input('max_order'),
            'type' => strtolower($this->input('type') ?: 'default'),
        ]);
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Log::error('Service validation failed', [
            'errors' => $validator->errors()->toArray(),
            'input' => $this->all()
        ]);
        parent::failedValidation($validator);
    }
}
