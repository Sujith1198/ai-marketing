@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-eye-fill text-warning me-2"></i>Product Watchlist</h2>
            <p class="text-muted mb-0">Track promising affiliate products for future re-analysis and campaign opportunities.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($products as $prod)
            @php $score = $prod->score; @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card-custom p-4 h-100 position-relative">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-secondary">{{ $prod->network->name ?? 'Network' }}</span>
                        @if($score)
                            <span class="badge {{ $score->badgeClass() }}">{{ $score->recommendation }}</span>
                        @endif
                    </div>

                    <h5 class="fw-bold mb-1">
                        <a href="{{ route('products.show', $prod->id) }}" class="text-dark text-decoration-none">{{ $prod->name }}</a>
                    </h5>
                    <p class="text-muted small mb-3">{{ $prod->category }}</p>

                    <div class="border-top pt-3 mt-auto d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fw-bold fs-5 text-primary">{{ $score ? $score->overall_opportunity_score : 0 }}/100</span>
                            <small class="text-muted d-block">Opportunity Score</small>
                        </div>

                        <div class="d-flex gap-2">
                            <form action="{{ route('watchlist.toggle', $prod->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Remove from Watchlist">
                                    <i class="bi bi-star-fill me-1"></i> Watching
                                </button>
                            </form>

                            <a href="{{ route('products.show', $prod->id) }}" class="btn btn-sm btn-primary">
                                View <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-star text-muted fs-1 mb-3 d-block"></i>
                <h5>No Products on Watchlist</h5>
                <p class="text-muted">You can add products to your watchlist from the Product Catalog or Opportunity Center.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary mt-2">Browse Product Catalog</a>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection
