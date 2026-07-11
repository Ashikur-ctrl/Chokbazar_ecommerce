<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'sale_price',
        'cost_price',
        'stock',
        'moq',
        'is_wholesale',
        'is_b2b_only',
        'low_stock_threshold',
        'sku',
        'tags',
        'image',
        'seo_title',
        'seo_description',
        'is_featured',
        'is_active',
        'visibility_status',
        'category_id',
        'seller_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'is_wholesale' => 'boolean',
        'is_b2b_only' => 'boolean',
        'moq' => 'integer',
    ];

    // Automatically generate slug from name
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (!$product->slug) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship with seller
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    // Relationship with price tiers
    public function priceTiers()
    {
        return $this->hasMany(PriceTier::class)->orderBy('min_quantity');
    }

    // Relationship with product images
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    // Get primary image
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->reviews()->where('is_approved', true)->latest();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        $query->where('is_active', true);

        if (Schema::hasColumn('products', 'visibility_status')) {
            $query->where(function ($query) {
                $query->whereNull('visibility_status')
                    ->orWhere('visibility_status', 'active');
            });
        }

        return $query;
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Laravel Scout search configuration
    public function searchableAs(): string
    {
        return 'products_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'sku' => $this->sku,
            'tags' => $this->tags,
            'price' => (float) $this->price,
            'sale_price' => (float) $this->sale_price,
            'category_id' => (int) $this->category_id,
            'seller_id' => $this->seller_id,
            'is_active' => (bool) $this->is_active,
            'stock' => (int) $this->stock,
            'created_at' => $this->created_at?->timestamp,
        ];
    }

    // Use slug for route model binding
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Accessors
    public function getCurrentPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute()
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->is_on_sale) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getIsInStockAttribute()
    {
        return $this->stock > 0;
    }

    public function getAverageRatingAttribute()
    {
        return round((float) $this->approvedReviews()->avg('rating'), 1);
    }

    public function getReviewsCountAttribute()
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get label badges for product UI (Sale, New, Hot, Trending, Featured)
     * Uses loaded counts when available to avoid N+1 queries.
     */
    public function getLabelsAttribute()
    {
        $labels = [];

        if ($this->is_on_sale) {
            $labels[] = 'Sale';
        }

        if ($this->created_at && $this->created_at->gt(now()->subDays(7))) {
            $labels[] = 'New';
        }

        // Use pre-loaded order_items_count when available for performance
        if (isset($this->order_items_count) && $this->order_items_count > 20) {
            $labels[] = 'Hot';
        }

        // Tag-based trending
        if (!empty($this->tags) && is_array($this->tags) && in_array('trending', array_map('strtolower', $this->tags))) {
            $labels[] = 'Trending';
        }

        if ($this->is_featured) {
            $labels[] = 'Featured';
        }

        return $labels;
    }
}
