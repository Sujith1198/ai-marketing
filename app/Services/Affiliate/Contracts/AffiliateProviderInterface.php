<?php

namespace App\Services\Affiliate\Contracts;

interface AffiliateProviderInterface
{
    public function providerKey(): string;

    public function capabilities(): array;

    public function testConnection(array $credentials): array;

    public function searchProducts(array $filters = []): array;

    public function getProduct(string $externalId): ?array;

    public function generateAffiliateLink(string $url, array $options = []): ?string;

    public function syncConversions(array $options = []): array;
}
