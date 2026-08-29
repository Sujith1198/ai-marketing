<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'affiliate_network_id' => ['required', 'exists:affiliate_networks,id'],
            'affiliate_account_id' => ['nullable', 'exists:affiliate_accounts,id'],
            'category' => ['nullable', 'string', 'max:100'],
            'subcategory' => ['nullable', 'string', 'max:100'],
            'brand' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'product_url' => ['nullable', 'url', 'regex:/^https?:\/\//i'],
            'affiliate_url' => ['required', 'url', 'regex:/^https?:\/\//i'],
            'image_url' => ['nullable', 'url', 'regex:/^https?:\/\//i'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'commission_type' => ['nullable', 'string', 'in:percentage,fixed'],
            'commission_value' => ['nullable', 'numeric', 'min:0'],
            'commission_notes' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:draft,active,watching,promote,rejected,archived'],
        ];
    }
}
