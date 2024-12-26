@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body">
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                    <h1 class="card-title text-center fw-bold">{{ $product->name }}</h1>
                    <p class="text-muted text-center">{{ $product->description }}</p>
                    <h3 class="text-center text-primary fw-bold">Price: ${{ number_format($product->price, 2) }}</h3>
                    <hr>
                    <h5 class="text-center mb-4">Enter Payment Details</h5>

                    <!-- Stripe Payment Form -->
                    <form action="{{ route('products.charge', $encryptedProductId) }}" method="POST" id="payment-form">
                        @csrf
                        <div id="card-element" class="mb-3 border rounded p-3"></div>
                        @if($errors->has('paymentMethodId'))
                            <div class="alert alert-danger mt-3">
                                {{ $errors->first('paymentMethodId') }}
                            </div>
                        @endif
                        <button id="payButton" class="btn btn-primary btn-lg w-100 fw-bold">Pay Now</button>
                    </form>

                    <div id="payment-errors" class="text-danger mt-3 text-center"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripe = Stripe('{{ config('cashier.key') }}');
        const elements = stripe.elements();

        // Card Element Styling
        const card = elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Roboto", sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a',
                },
            },
        });

        // Mount the card element
        card.mount('#card-element');

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            document.getElementById('payButton').disabled = true;
            const { paymentMethod, error } = await stripe.createPaymentMethod('card', card);

            if (error) {
                // Show error in the UI
                const errorElement = document.getElementById('payment-errors');
                errorElement.textContent = error.message;
            } else {
                // Add the PaymentMethod ID to the form and submit
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethodId');
                hiddenInput.setAttribute('value', paymentMethod.id);
                form.appendChild(hiddenInput);
                form.submit(); // Use native form submission here
            }
        });
    });
</script>

<style>
    body {
        background: linear-gradient(to right, #e3f2fd, #f8fafc);
    }

    .card {
        border-radius: 15px;
    }

    #card-element {
        background: #f8f9fa;
        border: 1px solid #ced4da;
    }

    #payButton {
        box-shadow: 0 4px 6px rgba(0, 123, 255, 0.3);
    }

    #payButton:hover {
        box-shadow: 0 6px 10px rgba(0, 86, 179, 0.5);
    }
</style>
@endsection
