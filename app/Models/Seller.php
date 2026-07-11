<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Seller extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company_name',
        'slug',
        'logo',
        'cover_image',
        'description',
        'business_documents',
        'bank_details',
        'national_id',
        'tin_number',
        'trade_license',
        'verification_status',
        'api_key',
        'fulfillment_method',
        'is_active',
        'commission_percentage',
        'return_policy',
        'shipping_days_min',
        'shipping_days_max',
        'rating',
        'rating_count',
        'total_revenue',
        'total_orders',
        'approved_at',
        'minimum_order_amount',
        'business_type',
        'year_established',
        'website_url',
        'whatsapp_number',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_percentage' => 'decimal:2',
        'rating' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'business_documents' => 'array',
        'bank_details' => 'array',
        'shipping_days_min' => 'integer',
        'shipping_days_max' => 'integer',
        'rating_count' => 'integer',
        'total_orders' => 'integer',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($seller) {
            if (empty($seller->slug) && $seller->company_name) {
                $seller->slug = Str::slug($seller->company_name);

                $baseSlug = $seller->slug;
                $counter = 1;
                while (static::where('slug', $seller->slug)->exists()) {
                    $seller->slug = $baseSlug . '-' . $counter++;
                }
            }
        });
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function fulfillmentRequests(): HasMany
    {
        return $this->hasMany(FulfillmentRequest::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function ledger(): HasMany
    {
        return $this->hasMany(SellerLedger::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function returnRequests(): HasMany
    {
        return $this->hasMany(ReturnRequest::class, 'seller_id');
    }

    public function getIsSuspendedAttribute(): bool
    {
        return $this->suspended_at !== null;
    }

    public function scopeNotSuspended($query)
    {
        return $query->whereNull('suspended_at');
    }

    public static function findByApiKey(string $apiKey): ?self
    {
        return static::where('api_key', $apiKey)->first();
    }

    public function generateApiKey(): string
    {
        $this->api_key = 'seller_' . bin2hex(random_bytes(32));
        $this->save();
        return $this->api_key;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopePendingApproval($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
