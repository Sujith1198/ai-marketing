<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Services\Product\ProductAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    protected int $productId;
    protected int $analysisId;

    public function __construct(int $productId, int $analysisId)
    {
        $this->productId = $productId;
        $this->analysisId = $analysisId;
    }

    public function handle(ProductAnalysisService $analysisService): void
    {
        $product = Product::find($this->productId);
        $analysis = ProductAnalysis::find($this->analysisId);

        if (!$product || !$analysis) {
            return;
        }

        $analysis->update(['status' => 'running']);

        try {
            $analysisService->executeAnalysis($product, $analysis);
        } catch (\Exception $e) {
            Log::error("AnalyzeProductJob Exception: " . $e->getMessage());
            $analysis->update([
                'status' => 'failed',
                'raw_ai_output' => ['error' => $e->getMessage()],
            ]);
        }
    }
}
