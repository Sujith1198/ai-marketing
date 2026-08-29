<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Services\Product\ProductAnalysisService;
use Illuminate\Http\Request;

class ProductAnalysisController extends Controller
{
    protected ProductAnalysisService $analysisService;

    public function __construct(ProductAnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    public function analyze(Product $product)
    {
        $analysis = $this->analysisService->initiateAnalysis($product, true);

        return redirect()->route('products.show', $product->id)
            ->with('success', "Product Analysis v{$analysis->analysis_version} initiated successfully!");
    }

    public function history(Product $product)
    {
        $analyses = ProductAnalysis::with('scores')
            ->where('product_id', $product->id)
            ->orderBy('analysis_version', 'desc')
            ->get();

        return view('products.analysis_history', compact('product', 'analyses'));
    }
}
