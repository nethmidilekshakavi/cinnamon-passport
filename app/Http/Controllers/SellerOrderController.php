<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SellerOrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = Order::where('brand_id', $request->user()->brand->id)
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Seller/Orders/Index', ['orders' => $orders]);
    }

    public function ship(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->brand_id === $request->user()->brand->id, 403);

        if (! $order->canBeShipped()) {
            return back()->withErrors(['order' => 'Only a new order can be marked as shipped.']);
        }

        $order->update(['status' => Order::SHIPPED, 'shipped_at' => now()]);

        return redirect()->route('seller.orders.index');
    }
}