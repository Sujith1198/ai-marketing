@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-file-earmark-spreadsheet text-primary me-2"></i>Import Products via CSV</h2>
            <p class="text-muted mb-0">Upload a CSV file containing affiliate product listings for batch creation and analysis.</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Products
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

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-custom p-4">
                <form action="{{ route('products.import.process') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold fs-5">Select CSV File</label>
                        <input type="file" name="csv_file" class="form-control form-control-lg" accept=".csv,.txt" required>
                        <small class="text-muted mt-1 d-block">Maximum upload size: 5MB (.csv file format)</small>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="bi bi-upload me-2"></i> Upload & Process CSV
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-info me-2"></i>CSV Field Specifications</h5>
                <p class="small text-muted mb-3">Your CSV header row should contain the following column names:</p>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Header Column</th>
                                <th>Required?</th>
                                <th>Example</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <tr><td><code>product_name</code></td><td><span class="badge bg-danger">Yes</span></td><td>Hostinger Cloud Startup</td></tr>
                            <tr><td><code>affiliate_url</code></td><td><span class="badge bg-danger">Yes</span></td><td>https://hostinger.com?REFERRAL=ai</td></tr>
                            <tr><td><code>network</code></td><td><span class="badge bg-secondary">Optional</span></td><td>Amazon, Digistore24, Hostinger</td></tr>
                            <tr><td><code>category</code></td><td><span class="badge bg-secondary">Optional</span></td><td>Hosting, Software</td></tr>
                            <tr><td><code>price</code></td><td><span class="badge bg-secondary">Optional</span></td><td>299.00</td></tr>
                            <tr><td><code>commission_type</code></td><td><span class="badge bg-secondary">Optional</span></td><td>percentage or fixed</td></tr>
                            <tr><td><code>commission_value</code></td><td><span class="badge bg-secondary">Optional</span></td><td>70</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
