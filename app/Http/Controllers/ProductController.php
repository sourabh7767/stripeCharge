<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use DB;
use Illuminate\Support\Facades\Crypt;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all(); 
        return view('products.index', compact('products'));
    }

    // Show the product details with the Stripe payment form
    public function buy($id)
    {
        $productId = Crypt::decrypt($id);
        $product = Product::findOrFail($productId); 
        $encryptedProductId = Crypt::encrypt($product->id);
        return view('products.buy', compact('product','encryptedProductId'));
    }

    // Process the Stripe charge using Laravel Cashier
    public function charge(Request $request, $id)
    {
        $id = Crypt::decrypt($id);
        $request->validate([
            'paymentMethodId' => 'required|string',
        ], [
            'paymentMethodId.required' => 'Please provide a payment method.',
            'paymentMethodId.string' => 'The payment method is invalid.',
        ]);
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Please log in to complete the payment.');
        }

        
        $product = Product::findOrFail($id);

        $paymentMethodId = $request->input('paymentMethodId');

        $user = $request->user();
        if (!$user->hasPaymentMethod()) {
            $user->createOrGetStripeCustomer();
            $user->addPaymentMethod($paymentMethodId);
        }

        try {
            
                $charge = $user->charge($product->price * 100, $paymentMethodId, [
                'return_url' => route('products.index')]);

                DB::table('transactions')->insert([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'amount' => $product->price,
                    'stripe_transaction_id' => $charge->id,
                    'status' => 'completed',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            return redirect()->route('products.index')->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
}
