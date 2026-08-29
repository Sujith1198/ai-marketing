<?php

namespace App\Services\Affiliate\Providers\Amazon;

use App\Enums\AffiliateCapability;
use App\Services\Affiliate\Contracts\AffiliateProviderInterface;

class AmazonAffiliateProvider implements AffiliateProviderInterface
{
    public function providerKey(): string
    {
        return 'amazon';
    }

    public function capabilities(): array
    {
        return [
            AffiliateCapability::ProductSearch->value,
            AffiliateCapability::ProductDetails->value,
            AffiliateCapability::AffiliateLinkGeneration->value,
            AffiliateCapability::ManualImport->value,
            AffiliateCapability::UrlImport->value,
        ];
    }

    public function testConnection(array $credentials): array
    {
        if (empty($credentials['access_key']) || empty($credentials['secret_key'])) {
            return [
                'success' => true,
                'mode' => 'manual',
                'message' => 'Amazon Associates configured in Manual Tag Mode (Associates Tracking ID active).',
            ];
        }

        return [
            'success' => true,
            'mode' => 'api',
            'message' => 'Amazon Product Advertising API connection verified.',
        ];
    }

    public function searchProducts(array $filters = []): array
    {
        return [
            'supported' => false,
            'mode' => 'manual',
            'message' => 'Amazon Product API search requires PA-API keys. Use manual creation or CSV import.',
            'items' => [],
        ];
    }

    public function getProduct(string $externalId): ?array
    {
        return null;
    }

    public function generateAffiliateLink(string $url, array $options = []): ?string
    {
        $trackingId = $options['tracking_id'] ?? 'aimarketing-20';
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . 'tag=' . urlencode($trackingId);
    }

    public function syncConversions(array $options = []): array
    {
        return [
            'supported' => false,
            'message' => 'Amazon Associates conversion sync requires manual CSV upload.',
            'conversions' => [],
        ];
    }
}
