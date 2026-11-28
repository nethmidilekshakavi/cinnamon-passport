<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    private const MAX_QUANTITY = 50;

    public function create(Request $request, Product $product): Response|RedirectResponse
    {
        abort_unless($product->is_published, 404);

        if ($this->blockedReason($request, $product) !== null) {
            return redirect()->route('market.show', $product->slug);
        }

        $product->load('brand:id,name,slug,district,country');

        $max = min($product->stock, self::MAX_QUANTITY);
        $quantity = min(max((int) $request->query('quantity', 1), 1), $max);

        return Inertia::render('Checkout/Create', [
            'product' => $product,
            'quantity' => $quantity,
            'maxQuantity' => $max,
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_published, 404);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:'.self::MAX_QUANTITY],
            'shipping_name' => ['required', 'string', 'max:120'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['nullable', 'string', 'max:20'],
            'shipping_country' => ['required', 'string', 'max:100'],
            'accept_prototype' => ['accepted'],
        ]);

        $order = DB::transaction(function () use ($request, $product, $data) {
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            $reason = $this->blockedReason($request, $locked);
            if ($reason !== null) {
                throw ValidationException::withMessages(['quantity' => $reason]);
            }

            $quantity = (int) $data['quantity'];
            if ($locked->stock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Only '.$locked->stock.' left in stock.',
                ]);
            }

            $locked->decrement('stock', $quantity);

            $order = Order::create([
                'user_id' => $request->user()->id,
                'brand_id' => $locked->brand_id,
                'product_id' => $locked->id,
                'product_name' => $locked->name,
                'product_size' => $locked->net_quantity.' '.$locked->unit,
                'unit_price' => $locked->price,
                'quantity' => $quantity,
                'total' => number_format((float) $locked->price * $quantity, 2, '.', ''),
                'shipping_name' => $data['shipping_name'],
                'shipping_address' => $data['shipping_address'],
                'shipping_city' => $data['shipping_city'],
                'shipping_postal_code' => $data['shipping_postal_code'] ?? null,
                'shipping_country' => $data['shipping_country'],
                'status' => Order::PLACED,
                'payment_method' => 'simulated',
                'placed_at' => now(),
            ]);

            $order->update(['order_number' => 'CT-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT)]);

            return $order;
        });

        return redirect()->route('orders.show', [$order, 'placed' => 1]);
    }

    private function blockedReason(Request $request, Product $product): ?string
    {
        if (! $product->is_published) {
            return 'This product is not available.';
        }
        if ($product->stock <= 0) {
            return 'This product is out of stock.';
        }
        if ($product->isExpired()) {
            return 'This product has expired.';
        }
        if ($request->user()->brand?->id === $product->brand_id) {
            return 'You cannot order your own product.';
        }

        return null;
    }
}