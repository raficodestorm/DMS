<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Cookie key & max items for guest wishlists
    |--------------------------------------------------------------------------
    */
    private const COOKIE_KEY  = 'guest_wishlist';
    private const COOKIE_DAYS = 30;

    /*
    |--------------------------------------------------------------------------
    | Toggle: Add or Remove a product from the wishlist
    |--------------------------------------------------------------------------
    */
    public function toggle(Request $request)
    {
        $productId = (int) $request->input('product_id');

        if (!$productId || !Product::where('id', $productId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        if (Auth::check()) {
            return $this->toggleDb($productId);
        }

        return $this->toggleCookie($request, $productId);
    }

    /*
    |--------------------------------------------------------------------------
    | Get all wishlist items (for modal)
    |--------------------------------------------------------------------------
    */
    public function items(Request $request)
    {
        if (Auth::check()) {
            $products = $this->getDbItems();
        } else {
            $products = $this->getCookieItems($request);
        }

        // Render each product as card HTML
        $html = '';
        foreach ($products as $product) {
            $html .= $this->renderWishlistCard($product);
        }

        return response()->json([
            'success' => true,
            'count'   => count($products),
            'html'    => $html ?: '<div class="wishlist-empty"><i class="far fa-heart"></i><p>Your wishlist is empty.</p></div>',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Get wishlist count (badge)
    |--------------------------------------------------------------------------
    */
    public function count(Request $request)
    {
        if (Auth::check()) {
            $count = Wishlist::where('user_id', Auth::id())->count();
        } else {
            $count = count($this->parseCookieIds($request));
        }

        return response()->json(['count' => $count]);
    }

    /*
    |--------------------------------------------------------------------------
    | Sync guest cookie wishlist to DB after login
    |--------------------------------------------------------------------------
    */
    public function syncAfterLogin(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false]);
        }

        $ids = $this->parseCookieIds($request);
        foreach ($ids as $productId) {
            if (Product::where('id', $productId)->exists()) {
                Wishlist::firstOrCreate([
                    'user_id'    => Auth::id(),
                    'product_id' => $productId,
                ]);
            }
        }

        // Clear the cookie by expiring it
        $count = Wishlist::where('user_id', Auth::id())->count();

        return response()
            ->json(['success' => true, 'count' => $count])
            ->cookie(self::COOKIE_KEY, '[]', -1);
    }

    /*
    |--------------------------------------------------------------------------
    | Private Helpers
    |--------------------------------------------------------------------------
    */

    private function toggleDb(int $productId): \Illuminate\Http\JsonResponse
    {
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message    = 'Removed from wishlist.';
        } else {
            Wishlist::create([
                'user_id'    => Auth::id(),
                'product_id' => $productId,
            ]);
            $inWishlist = true;
            $message    = 'Added to wishlist!';
        }

        $count = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success'     => true,
            'in_wishlist' => $inWishlist,
            'count'       => $count,
            'message'     => $message,
        ]);
    }

    private function toggleCookie(Request $request, int $productId): \Illuminate\Http\JsonResponse
    {
        $ids = $this->parseCookieIds($request);

        if (in_array($productId, $ids)) {
            $ids        = array_values(array_filter($ids, fn($id) => $id !== $productId));
            $inWishlist = false;
            $message    = 'Removed from wishlist.';
        } else {
            $ids[]      = $productId;
            $inWishlist = true;
            $message    = 'Added to wishlist!';
        }

        $cookieValue = json_encode(array_values($ids));

        return response()
            ->json([
                'success'     => true,
                'in_wishlist' => $inWishlist,
                'count'       => count($ids),
                'message'     => $message,
            ])
            ->cookie(self::COOKIE_KEY, $cookieValue, 60 * 24 * self::COOKIE_DAYS);
    }

    private function parseCookieIds(Request $request): array
    {
        $raw = $request->cookie(self::COOKIE_KEY, '[]');
        $ids = json_decode($raw, true);
        return is_array($ids) ? array_map('intval', $ids) : [];
    }

    private function getDbItems(): \Illuminate\Database\Eloquent\Collection
    {
        return Product::whereIn('id',
            Wishlist::where('user_id', Auth::id())->pluck('product_id')
        )->with(['activeRetailOffer'])->withSum('stocks', 'quantity')->get();
    }

    private function getCookieItems(Request $request): \Illuminate\Support\Collection
    {
        $ids = $this->parseCookieIds($request);
        if (empty($ids)) {
            return collect();
        }
        return Product::whereIn('id', $ids)->with(['activeRetailOffer'])->withSum('stocks', 'quantity')->get();
    }

    private function renderWishlistCard(Product $product): string
    {
        $today    = now()->toDateString();
        $offer    = $product->activeRetailOffer;

        $basePrice    = (float) ($product->price ?? 0);
        $deductionPct = (float) (\App\Models\Deduction::where('type', 'main')->value('retail_deduction') ?? 0);
        $priceAfterDeduction = $deductionPct > 0 ? ($basePrice * (1 - $deductionPct / 100)) : $basePrice;

        $offerDiscountVal = 0;
        $offerText        = null;

        if ($offer) {
            if ($offer->type === 'free_shipping') {
                $offerText = 'Free Shipping';
            } elseif ($offer->type === 'percentage') {
                $offerDiscountVal = $priceAfterDeduction * (float) $offer->discount_amount / 100;
                $offerText = ((int) $offer->discount_amount == (float) $offer->discount_amount
                    ? (int) $offer->discount_amount
                    : (float) $offer->discount_amount) . '% OFF';
            } else {
                $offerDiscountVal = (float) $offer->discount_amount;
                $offerText = '৳' . ((int) $offer->discount_amount == (float) $offer->discount_amount
                    ? (int) $offer->discount_amount
                    : number_format($offer->discount_amount, 2)) . ' OFF';
            }
        }

        $finalPrice    = round(max(0, $priceAfterDeduction - $offerDiscountVal));
        $originalPrice = round($basePrice);
        $hasDiscount   = $finalPrice < $originalPrice;
        $isInStock     = (bool) $product->is_in_stock;

        $imageUrl = $product->image
            ? asset($product->image)
            : null;

        $imgHtml = $imageUrl
            ? '<img src="' . e($imageUrl) . '" alt="' . e($product->name) . '" loading="lazy">'
            : '<div class="wl-card-no-img"><i class="fas fa-box-open"></i></div>';

        $priceHtml = $hasDiscount
            ? '<span class="wl-price">৳' . number_format($finalPrice) . '</span><del class="wl-old-price">৳' . number_format($originalPrice) . '</del>'
            : '<span class="wl-price">৳' . number_format($originalPrice) . '</span>';

        $badgeHtml = ($offer && $offerText)
            ? '<span class="wl-badge-offer"><i class="fas fa-bolt"></i> ' . e($offerText) . '</span>'
            : '<span class="wl-badge-new">R</span>';

        $stockClass = $isInStock ? '' : 'out-of-stock';
        $stockHtml  = $isInStock
            ? '<i class="fas fa-check-circle"></i> In Stock'
            : '<i class="fas fa-times-circle"></i> Out of Stock';

        $addToCartBtn = $isInStock
            ? '<button type="button" class="wl-add-cart-btn" data-id="' . $product->id . '" data-name="' . e($product->name) . '" onclick="addToCart(this); event.stopPropagation();"><i class="fas fa-shopping-bag"></i> Add to Cart</button>'
            : '<button type="button" class="wl-add-cart-btn" style="opacity:.6;cursor:not-allowed;" disabled><i class="fas fa-ban"></i> Out of Stock</button>';

        return '
<div class="wl-card" data-product-id="' . $product->id . '">
  <div class="wl-card-img">' . $imgHtml . $badgeHtml . '</div>
  <div class="wl-card-body">
    <p class="wl-card-name">' . e($product->name) . '</p>
    <div class="wl-price-row">' . $priceHtml . '</div>
    <div class="wl-stock ' . $stockClass . '">' . $stockHtml . '</div>
    <div class="wl-card-actions">
      ' . $addToCartBtn . '
      <button type="button" class="wl-remove-btn" onclick="removeFromWishlist(' . $product->id . ')" title="Remove"><i class="fas fa-trash-alt"></i></button>
    </div>
  </div>
</div>';
    }
}
