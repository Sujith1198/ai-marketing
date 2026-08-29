@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Product: {{ $product->name }}</h2>
            <p class="text-muted mb-0">Update product details, commission values, and affiliate links.</p>
        </div>
        <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Product
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-custom p-4">
        <form action="{{ route('products.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Affiliate Network <span class="text-danger">*</span></label>
                    <select name="affiliate_network_id" class="form-select" required>
                        @foreach($networks as $net)
                            <option value="{{ $net->id }}" {{ old('affiliate_network_id', $product->affiliate_network_id) == $net->id ? 'selected' : '' }}>
                                {{ $net->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Affiliate URL <span class="text-danger">*</span></label>
                    <input type="url" name="affiliate_url" class="form-control" value="{{ old('affiliate_url', $product->affiliate_url) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Direct Product URL (Optional)</label>
                    <input type="url" name="product_url" class="form-control" value="{{ old('product_url', $product->product_url) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $product->category) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Subcategory</label>
                    <input type="text" name="subcategory" class="form-control" value="{{ old('subcategory', $product->subcategory) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Brand / Vendor</label>
                    <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Currency</label>
                    <input type="text" name="currency" class="form-control" value="{{ old('currency', $product->currency ?? 'USD') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Commission Type</label>
                    <select name="commission_type" class="form-select">
                        <option value="percentage" {{ old('commission_type', $product->commission_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('commission_type', $product->commission_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Commission Value</label>
                    <input type="number" step="0.01" name="commission_value" class="form-control" value="{{ old('commission_value', $product->commission_value) }}">
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Product Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" {{ old('status', $product->status->value ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $product->status->value ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="watching" {{ old('status', $product->status->value ?? '') == 'watching' ? 'selected' : '' }}>Watching</option>
                        <option value="promote" {{ old('status', $product->status->value ?? '') == 'promote' ? 'selected' : '' }}>Ready to Promote</option>
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
