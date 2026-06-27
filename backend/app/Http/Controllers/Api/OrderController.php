<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $orders = Order::with('items', 'user')
                           ->orderByDesc('created_at')
                           ->paginate(50);
        } else {
            $orders = Order::with('items')
                           ->where('user_id', $user->id)
                           ->orderByDesc('created_at')
                           ->get();
        }

        return response()->json($orders);
    }

    public function show(Request $request, Order $order)
    {
        $user = $request->user();

        if (! $user->isAdmin() && $order->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json($order->load('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.sku'    => 'required|string|max:20',
            'items.*.name'   => 'required|string|max:200',
            'items.*.price'  => 'required|numeric|min:0',
            'items.*.qty'    => 'required|integer|min:1',
            'client_name'    => 'nullable|string|max:100',
            'client_email'   => 'nullable|email|max:255',
            'client_phone'   => 'nullable|string|max:20',
        ]);

        $user  = $request->user();
        $items = $request->items;
        $total = collect($items)->sum(fn($i) => $i['price'] * $i['qty']);

        $ref = 'LA-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));

        $order = Order::create([
            'reference'    => $ref,
            'user_id'      => $user?->id,
            'client_name'  => $user?->name  ?? strip_tags($request->client_name  ?? 'Guest'),
            'client_email' => $user?->email ?? $request->client_email,
            'client_phone' => $user?->phone ?? $request->client_phone,
            'total'        => $total,
            'status'       => 'pending',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id'         => $order->id,
                'product_sku'      => strtoupper(strip_tags($item['sku'])),
                'product_name'     => strip_tags($item['name']),
                'product_category' => isset($item['category']) ? strip_tags($item['category']) : null,
                'unit_price'       => $item['price'],
                'quantity'         => $item['qty'],
                'subtotal'         => $item['price'] * $item['qty'],
            ]);
        }

        return response()->json($order->load('items'), 201);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,paid,delivered,cancelled',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'paid') {
            $data['paid_at'] = now();
        }
        if ($request->status === 'delivered') {
            $data['delivered_at'] = now();
        }

        $order->update($data);

        return response()->json($order->fresh()->load('items'));
    }
}
