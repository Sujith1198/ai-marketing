<?php

namespace App\DTOs;

class AffiliateProductDTO
{
    public function __construct(
        public string $name,
        public string $affiliateUrl,
        public ?string $externalId = null,
        public ?string $productUrl = null,
        public ?string $category = null,
        public ?string $brand = null,
        public ?float $price = null,
        public ?string $currency = 'USD',
        public ?string $commissionType = 'percentage',
        public ?float $commissionValue = 0.0,
        public ?string $imageUrl = null,
        public ?string $description = null,
        public array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? $data['product_name'] ?? 'Untitled Product',
            affiliateUrl: $data['affiliate_url'] ?? '',
            externalId: $data['external_id'] ?? $data['external_product_id'] ?? null,
            productUrl: $data['product_url'] ?? null,
            category: $data['category'] ?? null,
            brand: $data['brand'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            currency: $data['currency'] ?? 'USD',
            commissionType: $data['commission_type'] ?? 'percentage',
            commissionValue: isset($data['commission_value']) ? (float) $data['commission_value'] : 0.0,
            imageUrl: $data['image_url'] ?? null,
            description: $data['description'] ?? null,
            metadata: $data['metadata'] ?? []
        );
    }
}
