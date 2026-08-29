<?php

namespace App\Services\Affiliate;

use App\Services\Affiliate\Contracts\AffiliateProviderInterface;
use App\Services\Affiliate\Providers\Amazon\AmazonAffiliateProvider;
use App\Services\Affiliate\Providers\Digistore24\Digistore24AffiliateProvider;
use App\Services\Affiliate\Providers\Hostinger\HostingerAffiliateProvider;
use InvalidArgumentException;

class AffiliateProviderManager
{
    protected array $providers = [];

    public function __construct()
    {
        $this->register(new AmazonAffiliateProvider());
        $this->register(new Digistore24AffiliateProvider());
        $this->register(new HostingerAffiliateProvider());
    }

    public function register(AffiliateProviderInterface $provider): void
    {
        $this->providers[$provider->providerKey()] = $provider;
    }

    public function resolve(string $providerKey): AffiliateProviderInterface
    {
        if (!isset($this->providers[$providerKey])) {
            // Default fallback to Amazon or Hostinger for custom keys
            return $this->providers['hostinger'] ?? reset($this->providers);
        }

        return $this->providers[$providerKey];
    }

    public function all(): array
    {
        return $this->providers;
    }

    public function hasCapability(string $providerKey, string $capability): bool
    {
        $provider = $this->resolve($providerKey);
        return in_array($capability, $provider->capabilities());
    }
}
