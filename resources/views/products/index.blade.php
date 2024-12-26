@extends('layouts.main')

@section('content')
<div class="text-center mb-5">
    <h1 class="display-5 fw-bold">Our Products</h1>
    <p class="text-muted">Explore our range of exclusive products. Click "Buy Now" to get started!</p>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
</div>


<div class="row row-cols-1 row-cols-md-3 g-4">
    @foreach ($products as $product)
        <div class="col">
            <div class="card h-100 shadow-lg border-0 product-card">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold">{{ $product->name }}</h5>
                    <p class="card-text text-muted">{{ Str::limit($product->description, 100) }}</p>
                    <h6 class="text-primary fw-bold fs-4">${{ number_format($product->price, 2) }}</h6>
                </div>
                <div class="card-footer bg-transparent border-0 text-center">
                    <a href="{{ route('products.buy', Crypt::encrypt($product->id)) }}" class="btn btn-primary btn-lg w-100 fw-bold">Buy Now</a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<style>
    /* Page Background */
    body {
        background: linear-gradient(to right, #f8fafc, #e3f2fd);
        font-family: 'Arial', sans-serif;
    }

    /* Product Card Styling */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 15px;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    /* Typography */
    h1 {
        color: #212529;
    }

    .card-title {
        color: #2d3436;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
        box-shadow: 0 6px 10px rgba(0, 86, 179, 0.5);
    }
</style>
@endsection
