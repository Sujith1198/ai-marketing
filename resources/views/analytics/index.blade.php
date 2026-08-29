@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow text-primary me-2"></i>Analytics & Performance</h2>
    <p class="text-muted mb-0">Track link clicks, conversions, commission revenue, and CTR across networks.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-custom p-3">
            <span class="text-muted small fw-semibold text-uppercase">Total Clicks</span>
            <h3 class="fw-bold mb-0 mt-1">{{ number_format($clicksCount) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <span class="text-muted small fw-semibold text-uppercase">Total Conversions</span>
            <h3 class="fw-bold mb-0 mt-1 text-success">{{ number_format($conversionsCount) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <span class="text-muted small fw-semibold text-uppercase">Average CTR</span>
            <h3 class="fw-bold mb-0 mt-1 text-info">{{ $ctr }}%</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card-custom p-3">
            <span class="text-muted small fw-semibold text-uppercase">Affiliate Revenue</span>
            <h3 class="fw-bold mb-0 mt-1 text-primary">${{ number_format($totalRevenue, 2) }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3">Top Performing Products</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Product</th><th>Opportunity Score</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $prod)
                            <tr>
                                <td class="fw-semibold">{{ $prod->name }}</td>
                                <td><span class="badge bg-primary fs-6">{{ optional($prod->score)->overall_opportunity_score ?? 'N/A' }}/100</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3">Active Campaigns Overview</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Campaign</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($topCampaigns as $camp)
                            <tr>
                                <td class="fw-semibold">{{ $camp->name }}</td>
                                <td><span class="badge {{ $camp->badgeClass() }}">{{ $camp->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
