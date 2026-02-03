<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $total = $cartItems->sum(function ($item) {
            return $item->getSubtotal();
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:100',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        if (!$product->in_stock || $product->stock_quantity < ($validated['quantity'] ?? 1)) {
            return response()->json([
                'success' => false,
                'message' => 'Product is out of stock'
            ], 400);
        }

        $sessionId = $this->getSessionId();

        $cartItem = CartItem::where('session_id', $sessionId)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += ($validated['quantity'] ?? 1);
            $cartItem->save();
        } else {
            CartItem::create([
                'session_id' => $sessionId,
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $validated['quantity'] ?? 1,
                'price' => $product->getCurrentPrice(),
            ]);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => $cartCount
        ]);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        if ($cartItem->session_id !== $this->getSessionId()) {
            abort(403);
        }

        $cartItem->update([
            'quantity' => $validated['quantity']
        ]);

        return response()->json([
            'success' => true,
            'subtotal' => $cartItem->getSubtotal(),
            'total' => $this->getCartTotal()
        ]);
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->session_id !== $this->getSessionId()) {
            abort(403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'cart_count' => $this->getCartCount(),
            'total' => $this->getCartTotal()
        ]);
    }

    public function count()
    {
        return response()->json([
            'count' => $this->getCartCount()
        ]);
    }

    protected function getSessionId()
    {
        if (!session()->has('cart_session_id')) {
            session()->put('cart_session_id', uniqid('cart_', true));
        }

        return session()->get('cart_session_id');
    }

    protected function getCartItems()
    {
        return CartItem::with('product')
            ->where('session_id', $this->getSessionId())
            ->get();
    }

    protected function getCartCount()
    {
        return CartItem::where('session_id', $this->getSessionId())
            ->sum('quantity');
    }

    protected function getCartTotal()
    {
        return $this->getCartItems()->sum(function ($item) {
            return $item->getSubtotal();
        });
    }
}
