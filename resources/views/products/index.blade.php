@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-box-seam-fill text-primary me-2"></i>Product Manager</h2>
            <p class="text-muted mb-0">Discover, import, analyze, and manage high-converting affiliate products.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.import') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import CSV
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters Section -->
    <div class="card-custom p-3 mb-4">
        <form method="GET" action="{{ route('products.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Search name, brand..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="network_id" class="form-select">
                    <option value="">All Networks</option>
                    @foreach($networks as $net)
                        <option value="{{ $net->id }}" {{ request('network_id') == $net->id ? 'selected' : '' }}>{{ $net->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="watching" {{ request('status') == 'watching' ? 'selected' : '' }}>Watching</option>
                    <option value="promote" {{ request('status') == 'promote' ? 'selected' : '' }}>Promote</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-funnel-fill"></i></button>
            </div>
        </form>
    </div>

    <!-- Product Table -->
    <div class="card-custom p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Product</th>
                        <th>Network</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Commission</th>
                        <th>Overall Score</th>
                        <th>Recommendation</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $prod)
                        @php $score = $prod->score; @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3 border text-center" style="width:48px; height:48px;">
                                        <i class="bi bi-box text-muted fs-4"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('products.show', $prod->id) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $prod->name }}
                                        </a>
                                        <small class="text-muted d-block">{{ $prod->brand ?? 'Generic' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary">{{ $prod->network->name ?? 'Network' }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ $prod->category ?? 'General' }}</span></td>
                            <td>{{ $prod->currency }} {{ number_format($prod->price, 2) }}</td>
                            <td>
                                <strong class="text-success">
                                    {{ $prod->commission_value }}{{ $prod->commission_type === 'percentage' ? '%' : ' $' }}
                                </strong>
                            </td>
                            <td>
                                <div class="fw-bold fs-6 text-primary">{{ $score ? $score->overall_opportunity_score : 0 }}/100</div>
                            </td>
                            <td>
                                @if($score)
                                    <span class="badge {{ $score->badgeClass() }}">{{ $score->recommendation }}</span>
                                @else
                                    <span class="badge bg-light text-muted border">Unanalyzed</span>
                                @endif
                            </td>
                            <td><span class="badge {{ $prod->status->badgeClass() }}">{{ $prod->status->label() }}</span></td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    <a href="{{ route('products.show', $prod->id) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $prod->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('watchlist.toggle', $prod->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $prod->status->value === 'watching' ? 'btn-warning' : 'btn-outline-warning' }}" title="Watchlist">
                                            <i class="bi bi-star"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <p class="text-muted mb-0">No products found matching filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $products->appends(request()->query())->links() }}
    </div>
</div>
@endsection
