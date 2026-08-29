@extends('layouts.app')

@section('title', 'Add New Affiliate Product')

@section('content')
<div class="mb-4">
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm mb-2"><i class="bi bi-arrow-left me-1"></i> Back to Products</a>
    <h2 class="fw-bold mb-1">Add Affiliate Product</h2>
    <p class="text-muted mb-0">Enter product details to trigger automated AI research and scoring.</p>
</div>

<div class="card-custom p-4 max-w-700">
    <form action="{{ route('products.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="affiliate_network_id" class="form-label fw-semibold">Affiliate Network</label>
            <select class="form-select" id="affiliate_network_id" name="affiliate_network_id" required>
                @foreach($networks as $network)
                    <option value="{{ $network->id }}">{{ $network->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Product Name</label>
            <input type="text" class="form-control" id="name" name="name" required placeholder="e.g. Hostinger Premium Web Hosting">
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="product_url" class="form-label fw-semibold">Original Product URL</label>
                <input type="url" class="form-control" id="product_url" name="product_url" required placeholder="https://...">
            </div>
            <div class="col-md-6">
                <label for="affiliate_url" class="form-label fw-semibold">Your Affiliate Referral URL</label>
                <input type="url" class="form-control" id="affiliate_url" name="affiliate_url" required placeholder="https://...?referral=you">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="category" class="form-label fw-semibold">Category</label>
                <input type="text" class="form-control" id="category" name="category" placeholder="SaaS / Fitness / Electronics">
            </div>
            <div class="col-md-4">
                <label for="brand" class="form-label fw-semibold">Brand</label>
                <input type="text" class="form-control" id="brand" name="brand" placeholder="Brand Name">
            </div>
            <div class="col-md-4">
                <label for="price" class="form-label fw-semibold">Price ($)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="49.99">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="commission_value" class="form-label fw-semibold">Commission Value</label>
                <input type="number" step="0.01" class="form-control" id="commission_value" name="commission_value" placeholder="e.g. 50">
            </div>
            <div class="col-md-6">
                <label for="commission_type" class="form-label fw-semibold">Commission Type</label>
                <select class="form-select" id="commission_type" name="commission_type">
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount ($)</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label fw-semibold">Product Description</label>
            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief summary of product feature & value proposition..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary px-4 fw-semibold">Save & Analyze Product with AI</button>
    </form>
</div>
@endsection
