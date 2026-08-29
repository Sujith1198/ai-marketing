<?php

namespace App\Services\Product;

use App\Models\Product;

class ProductDataCompletenessService
{
    /**
     * Calculate 0-100% Data Completeness Score and missing fields list.
     */
    public function calculate(Product $product): array
    {
        $fields = [
            'name' => 15,
            'category' => 10,
            'description' => 15,
            'affiliate_url' => 20,
            'product_url' => 10,
            'price' => 10,
            'commission_value' => 10,
            'image_url' => 5,
            'brand' => 5,
        ];

        $earnedScore = 0;
        $missingFields = [];

        foreach ($fields as $field => $weight) {
            $value = $product->{$field};
            if (!empty($value) && $value > 0) {
                $earnedScore += $weight;
            } else {
                $missingFields[] = str_replace('_', ' ', ucfirst($field));
            }
        }

        $completenessScore = min(100, max(0, $earnedScore));

        return [
            'score' => $completenessScore,
            'missing_fields' => $missingFields,
            'is_complete' => $completenessScore >= 80,
            'label' => $completenessScore >= 80 ? 'High Confidence' : ($completenessScore >= 50 ? 'Medium Confidence' : 'Low Data Confidence'),
        ];
    }
}
