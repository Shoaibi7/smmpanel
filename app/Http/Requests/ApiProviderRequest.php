<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiProviderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $providerId = $this->route('api_provider') ?? $this->route('id');

        return [
            'api_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:100|unique:api_providers,short_name,' . $providerId,
            'api_url' => 'required|url|max:500',
            'api_key' => 'required|string|min:5',
            'status' => 'required|in:enabled,disabled',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'api_name.required' => 'API provider name is required.',
            'api_name.max' => 'API provider name cannot exceed 255 characters.',
            'short_name.required' => 'Short name is required.',
            'short_name.unique' => 'This short name is already taken.',
            'api_url.required' => 'API URL is required.',
            'api_url.url' => 'Please provide a valid URL format.',
            'api_url.max' => 'API URL cannot exceed 500 characters.',
            'api_key.required' => 'API key is required.',
            'api_key.min' => 'API key must be at least 5 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be either enabled or disabled.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'short_name' => str()->slug($this->short_name ?? $this->api_name),
        ]);
    }
}
