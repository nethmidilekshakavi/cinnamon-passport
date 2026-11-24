<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['brand:id,name,slug', 'product:id,slug'])
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Orders/Index', ['orders' => $orders]);
    }

    public function show(Request $request, Order $order): Response
    {
        $this->ownOrder($request, $order);

        $order->load(['brand:id,name,slug,district,country', 'product:id,slug']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
            'justPlaced' => $request->boolean('placed'),
            'canCancel' => $order->canBeCancelled(),
            'canMarkReceived' => $order->canBeMarkedReceived(),
            'review' => $order->review,
            'canReview' => $order->status === Order::DELIVERED && ! $order->review()->exists(),
        ]);
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        $this->ownOrder($request, $order);

        if (! $order->canBeCancelled()) {
            return back()->withErrors(['order' => 'Only an order that has not shipped can be cancelled.']);
        }

        DB::transaction(function () use ($order) {
            if ($order->product_id) {
                Product::whereKey($order->product_id)->increment('stock', $order->quantity);
            }

            $order->update(['status' => Order::CANCELLED, 'cancelled_at' => now()]);
        });

        return redirect()->route('orders.show', $order);
    }

    public function received(Request $request, Order $order): RedirectResponse
    {
        $this->ownOrder($request, $order);

        if (! $order->canBeMarkedReceived()) {
            return back()->withErrors(['order' => 'You can mark an order as received only after it has shipped.']);
        }

        $order->update(['status' => Order::DELIVERED, 'delivered_at' => now()]);

        return redirect()->route('orders.show', $order);
    }

    private function ownOrder(Request $request, Order $order): void
    {
        abort_unless($order->user_id === $request->user()->id, 403);
    }
}