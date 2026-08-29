<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\Product\ProductDataCompletenessService;
use Tests\TestCase;

class ProductCompletenessTest extends TestCase
{
    public function test_completeness_calculation()
    {
        $completenessService = new ProductDataCompletenessService();

        $productFull = new Product([
            'name' => 'Hostinger VPS 4',
            'category' => 'Hosting',
            'description' => 'Scalable AI server hosting',
            'affiliate_url' => 'https://hostinger.com?REFERRAL=ai',
            'product_url' => 'https://hostinger.com/vps',
            'price' => 19.99,
            'commission_value' => 70,
            'image_url' => 'https://hostinger.com/logo.png',
            'brand' => 'Hostinger',
        ]);

        $resultFull = $completenessService->calculate($productFull);
        $this->assertEquals(100, $resultFull['score']);
        $this->assertTrue($resultFull['is_complete']);

        $productPartial = new Product([
            'name' => 'Partial Product',
            'affiliate_url' => 'https://example.com',
        ]);

        $resultPartial = $completenessService->calculate($productPartial);
        $this->assertLessThan(50, $resultPartial['score']);
        $this->assertFalse($resultPartial['is_complete']);
    }
}
