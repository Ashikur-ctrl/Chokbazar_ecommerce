<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function index()
    {
        $items = Wishlist::with('product.category')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('items'));
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();

            if (request()->wantsJson()) {
                return response()->json(['success' => true, 'removed' => true, 'message' => 'Removed from wishlist.']);
            }

            return back()->with('success', 'Removed from wishlist.');
        }

        $entry = Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'added' => true, 'message' => 'Added to wishlist.']);
        }

        return back()->with('success', 'Added to wishlist.');
    }

    public function destroy(Wishlist $wishlist)
    {
        abort_unless($wishlist->user_id === auth()->id(), 403);

        $wishlist->delete();

        return back()->with('success', 'Removed from wishlist.');
    }
}
