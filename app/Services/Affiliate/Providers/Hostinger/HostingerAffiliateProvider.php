<?php

namespace App\Services\Affiliate\Providers\Hostinger;

use App\Enums\AffiliateCapability;
use App\Services\Affiliate\Contracts\AffiliateProviderInterface;

class HostingerAffiliateProvider implements AffiliateProviderInterface
{
    public function providerKey(): string
    {
        return 'hostinger';
    }

    public function capabilities(): array
    {
        return [
            AffiliateCapability::ManualImport->value,
            AffiliateCapability::UrlImport->value,
            AffiliateCapability::AffiliateLinkGeneration->value,
            AffiliateCapability::CommissionData->value,
        ];
    }

    public function testConnection(array $credentials): array
    {
        return [
            'success' => true,
            'mode' => 'manual',
            'message' => 'Hostinger Affiliate Partner tracking active (hPanel manual referral mode).',
        ];
    }

    public function searchProducts(array $filters = []): array
    {
        return [
            'supported' => false,
            'mode' => 'manual',
            'message' => 'Hostinger Partner Portal operates via manual link & campaign setup.',
            'items' => [],
        ];
    }

    public function getProduct(string $externalId): ?array
    {
        return null;
    }

    public function generateAffiliateLink(string $url, array $options = []): ?string
    {
        $referralId = $options['tracking_id'] ?? 'hostinger_ai';
        $separator = str_contains($url, '?') ? '&' : '?';
        return $url . $separator . 'REFERRALCODE=' . urlencode($referralId);
    }

    public function syncConversions(array $options = []): array
    {
        return [
            'supported' => false,
            'message' => 'Hostinger conversion sync operates via manual hPanel report import.',
            'conversions' => [],
        ];
    }
}
