<?php

namespace App\Enums;

enum AffiliateCapability: string
{
    case ProductSearch = 'product_search';
    case ProductDetails = 'product_details';
    case AffiliateLinkGeneration = 'affiliate_link_generation';
    case CommissionData = 'commission_data';
    case ConversionSync = 'conversion_sync';
    case OrderSync = 'order_sync';
    case ApiSync = 'api_sync';
    case ManualImport = 'manual_import';
    case UrlImport = 'url_import';

    public function label(): string
    {
        return str_replace('_', ' ', ucwords($this->value, '_'));
    }
}
