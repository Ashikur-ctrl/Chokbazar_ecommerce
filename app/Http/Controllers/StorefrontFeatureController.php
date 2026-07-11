<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\ContactMessage;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserBehavior;
use Illuminate\Http\Request;

class StorefrontFeatureController extends Controller
{
    public function compare(Request $request)
    {
        $ids = collect(explode(',', (string) $request->query('products')))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(4);

        $products = Product::with('category')
            ->whereIn('id', $ids)
            ->get();

        return view('pages.compare', compact('products'));
    }

    public function addToCompare(Product $product)
    {
        $compare = collect(session('compare', []))
            ->push($product->id)
            ->unique()
            ->take(4)
            ->values()
            ->all();

        session(['compare' => $compare]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'added' => true, 'message' => 'Product added to comparison.']);
        }

        return back()->with('success', 'Product added to comparison.');
    }

    public function clearCompare()
    {
        session()->forget('compare');

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'cleared' => true, 'message' => 'Comparison cleared.']);
        }

        return redirect()->route('compare')->with('success', 'Comparison cleared.');
    }

    public function recentlyViewed()
    {
        $productIds = UserBehavior::where(function ($query) {
                $query->where('user_id', auth()->id())
                    ->orWhere('session_id', session()->getId());
            })
            ->where('action', 'view')
            ->latest('created_at')
            ->pluck('product_id')
            ->unique()
            ->take(12);

        $products = Product::with('category')
            ->whereIn('id', $productIds)
            ->get();

        return view('pages.recently-viewed', compact('products'));
    }

    public function searchSuggestions(Request $request)
    {
        $term = trim((string) $request->query('q'));

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $products = Product::active()
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                    ->orWhere('sku', 'like', "%{$term}%");
            })
            ->limit(8)
            ->get(['id', 'name', 'slug', 'price', 'sale_price'])
            ->map(fn ($product) => [
                'name' => $product->name,
                'url' => route('shop.product', $product),
                'price' => taka($product->current_price),
            ]);

        return $products;
    }

    public function tracking(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()?->isAdmin(), 403);

        return view('orders.tracking', compact('order'));
    }

    public function addresses()
    {
        $addresses = Address::where('user_id', auth()->id())->latest()->get();

        return view('account.addresses', compact('addresses'));
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'label' => 'required|string|max:50',
            'name' => 'required|string|max:120',
            'phone' => 'nullable|string|max:30',
            'city' => 'nullable|string|max:80',
            'area' => 'nullable|string|max:80',
            'address' => 'required|string|max:1000',
            'is_default' => 'nullable|boolean',
        ]);

        if ($request->boolean('is_default')) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        Address::create($validated + [
            'user_id' => auth()->id(),
            'is_default' => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Address saved.');
    }

    public function deleteAddress(Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);

        $address->delete();

        return back()->with('success', 'Address removed.');
    }

    public function contactStore(Request $request)
    {
        ContactMessage::create($request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'subject' => 'required|string|max:180',
            'message' => 'required|string|max:2000',
        ]));

        return back()->with('success', 'Thanks! Your message has been received.');
    }
}
