<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status !== Order::DELIVERED) {
            return back()->withErrors(['review' => 'You can rate a seller only after the order is delivered.']);
        }

        if ($order->review()->exists()) {
            return back()->withErrors(['review' => 'You already rated this order.']);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'brand_id' => $order->brand_id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return redirect()->route('orders.show', $order);
    }
}