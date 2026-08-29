@extends('layouts.app')

@section('title', 'Products Management')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h2 class="fw-bold mb-1">Affiliate Products Manager</h2>
        <p class="text-muted mb-0">Manage products, trigger AI analysis, and calculate opportunity scores.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add New Product</a>
</div>

<div class="card-custom p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Network</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Commission</th>
                    <th>AI Score</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $product->name }}</div>
                            <span class="extra-small text-muted">{{ $product->brand }}</span>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ optional($product->network)->name }}</span></td>
                        <td>{{ $product->category ?? 'General' }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->commission_value }} ({{ $product->commission_type }})</td>
                        <td>
                            @if($product->score)
                                <span class="badge bg-primary fs-6">{{ $product->score->overall_opportunity_score }}/100</span>
                            @else
                                <span class="badge bg-secondary">Unanalyzed</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <form action="{{ route('products.analyze', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-info me-1"><i class="bi bi-cpu me-1"></i> Re-Analyze</button>
                            </form>
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No products created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
