<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImportRequest;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Services\Product\ProductImportService;
use Illuminate\Http\Request;

class ProductImportController extends Controller
{
    protected ProductImportService $importService;

    public function __construct(ProductImportService $importService)
    {
        $this->importService = $importService;
    }

    public function showImportForm()
    {
        return view('products.import');
    }

    public function processImport(ProductImportRequest $request)
    {
        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        $result = $this->importService->processImport($path, auth()->id());

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'csv_imported',
            'entity_type' => Product::class,
            'metadata' => [
                'imported' => $result['imported'],
                'skipped' => $result['skipped'],
                'failed' => $result['failed'],
            ],
        ]);

        return redirect()->route('products.index')->with('success', "CSV Import Complete! Imported: {$result['imported']}, Skipped: {$result['skipped']}, Failed: {$result['failed']}.");
    }
}
