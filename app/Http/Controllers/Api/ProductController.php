<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'sku' => $product->sku,
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'current_price' => $product->getCurrentPrice(),
            'stock_quantity' => $product->stock_quantity,
            'in_stock' => $product->in_stock,
            'featured_image_url' => $product->getFeaturedImageUrl(),
            'image_urls' => $product->getImageUrls(),
            'specifications' => $product->specifications,
            'has_discount' => $product->hasDiscount(),
            'discount_percentage' => $product->getDiscountPercentage(),
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
            ] : null,
        ]);
    }

    public function index(Request $request)
    {
        $query = Product::with('category')->active();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('featured')) {
            $query->featured();
        }

        if ($request->has('in_stock')) {
            $query->inStock();
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate($request->get('per_page', 20));

        return response()->json($products);
    }
}
