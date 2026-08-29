<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\ActivityLog;
use App\Models\AffiliateAccount;
use App\Models\AffiliateNetwork;
use App\Models\Product;
use App\Models\ProductAnalysis;
use App\Services\Product\ProductAnalysisService;
use App\Services\Product\ProductDataCompletenessService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected ProductDataCompletenessService $completenessService;

    public function __construct(ProductDataCompletenessService $completenessService)
    {
        $this->completenessService = $completenessService;
    }

    public function index(Request $request)
    {
        $query = Product::with(['network', 'account', 'score']);

        // Network Filter
        if ($request->filled('network_id')) {
            $query->where('affiliate_network_id', $request->input('network_id'));
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(12);

        $networks = AffiliateNetwork::where('is_active', true)->get();
        $categories = Product::distinct()->pluck('category')->filter()->values();

        return view('products.index', compact('products', 'networks', 'categories'));
    }

    public function create()
    {
        $networks = AffiliateNetwork::where('is_active', true)->get();
        $accounts = AffiliateAccount::where('user_id', auth()->id())->get();

        return view('products.create', compact('networks', 'accounts'));
    }

    public function store(ProductStoreRequest $request)
    {
        $affiliateUrl = $request->input('affiliate_url');

        // Check for duplicate product
        $existing = Product::where('affiliate_url', $affiliateUrl)->first();
        if ($existing && !$request->boolean('confirm_duplicate')) {
            return back()->withInput()->with('warning_duplicate', "A product with this affiliate URL already exists: '{$existing->name}'. Click submit again to confirm duplicate creation.");
        }

        $product = Product::create(array_merge($request->validated(), [
            'user_id' => auth()->id(),
            'slug' => Str::slug($request->input('name')) . '-' . Str::random(5),
            'source' => 'manual',
            'status' => $request->input('status', 'draft'),
        ]));

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_created',
            'entity_type' => Product::class,
            'entity_id' => $product->id,
            'metadata' => ['product_name' => $product->name],
        ]);

        // Trigger initial AI analysis
        app(ProductAnalysisService::class)->initiateAnalysis($product, true);

        return redirect()->route('products.show', $product->id)
            ->with('success', "Product '{$product->name}' created and AI Analysis initiated!");
    }

    public function show(Product $product)
    {
        $product->load(['network', 'account', 'analysis', 'score', 'analyses.scores', 'campaigns']);
        
        $completeness = $this->completenessService->calculate($product);
        $scoreHistory = $product->scores()->with('analysis')->take(10)->get();

        return view('products.show', compact('product', 'completeness', 'scoreHistory'));
    }

    public function edit(Product $product)
    {
        $networks = AffiliateNetwork::where('is_active', true)->get();
        $accounts = AffiliateAccount::where('user_id', auth()->id())->get();

        return view('products.edit', compact('product', 'networks', 'accounts'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->update($request->validated());

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_edited',
            'entity_type' => Product::class,
            'entity_id' => $product->id,
            'metadata' => ['product_name' => $product->name],
        ]);

        return redirect()->route('products.show', $product->id)
            ->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->update(['status' => 'archived']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_archived',
            'entity_type' => Product::class,
            'entity_id' => $product->id,
            'metadata' => ['product_name' => $name],
        ]);

        return redirect()->route('products.index')->with('success', "Product '{$name}' archived.");
    }
}
