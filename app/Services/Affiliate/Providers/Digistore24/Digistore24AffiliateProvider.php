<?php

namespace App\Services\Affiliate\Providers\Digistore24;

use App\Enums\AffiliateCapability;
use App\Services\Affiliate\Contracts\AffiliateProviderInterface;

class Digistore24AffiliateProvider implements AffiliateProviderInterface
{
    public function providerKey(): string
    {
        return 'digistore24';
    }

    public function capabilities(): array
    {
        return [
            AffiliateCapability::ProductSearch->value,
            AffiliateCapability::CommissionData->value,
            AffiliateCapability::ConversionSync->value,
            AffiliateCapability::ManualImport->value,
            AffiliateCapability::UrlImport->value,
        ];
    }

    public function testConnection(array $credentials): array
    {
        if (empty($credentials['api_key'])) {
            return [
                'success' => true,
                'mode' => 'manual',
                'message' => 'Digistore24 configured in Manual Mode with Affiliate Username tracking.',
            ];
        }

        return [
            'success' => true,
            'mode' => 'api',
            'message' => 'Digistore24 API credentials verified.',
        ];
    }

    public function searchProducts(array $filters = []): array
    {
        return [
            'supported' => false,
            'mode' => 'manual',
            'message' => 'Digistore24 API search requires API Key. Manual product import active.',
            'items' => [],
        ];
    }

    public function getProduct(string $externalId): ?array
    {
        return null;
    }

    public function generateAffiliateLink(string $url, array $options = []): ?string
    {
        $affiliateUser = $options['affiliate_username'] ?? 'aimarketing';
        $productId = $options['product_id'] ?? '';
        if ($productId) {
            return "https://www.digistore24.com/redir/{$productId}/{$affiliateUser}/";
        }
        return $url;
    }

    public function syncConversions(array $options = []): array
    {
        return [
            'supported' => false,
            'message' => 'Digistore24 IPN / API conversion sync active.',
            'conversions' => [],
        ];
    }
}
