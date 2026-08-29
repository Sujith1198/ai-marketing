<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\Product\ProductAIReviewService;
use Illuminate\Http\Request;

class ProductAIReviewController extends Controller
{
    protected ProductAIReviewService $reviewService;

    public function __construct(ProductAIReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function askAiTeam(Product $product)
    {
        $review = $this->reviewService->reviewProduct($product);

        return view('products.ai_review', compact('product', 'review'));
    }
}
