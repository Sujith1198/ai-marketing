<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Product;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function index()
    {
        $products = Product::with(['network', 'score', 'analysis'])
            ->where('status', 'watching')
            ->orderBy('updated_at', 'desc')
            ->paginate(12);

        return view('watchlist.index', compact('products'));
    }

    public function toggle(Product $product)
    {
        $newStatus = $product->status->value === 'watching' ? 'active' : 'watching';
        $product->update(['status' => $newStatus]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $newStatus === 'watching' ? 'product_added_to_watchlist' : 'product_removed_from_watchlist',
            'entity_type' => Product::class,
            'entity_id' => $product->id,
            'metadata' => ['product_name' => $product->name],
        ]);

        return back()->with('success', "Product '{$product->name}' " . ($newStatus === 'watching' ? 'added to Watchlist.' : 'removed from Watchlist.'));
    }
}
