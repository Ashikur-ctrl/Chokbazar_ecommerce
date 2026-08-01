<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_path',
        'alt_text',
        'is_primary',
        'sort_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // Relationship with product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor for full image URL
    public function getImageUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($this->image_path, ['http://', 'https://'])) {
            return $this->image_path;
        }

        if (\Illuminate\Support\Str::startsWith($this->image_path, 'images/')) {
            return asset($this->image_path);
        }

        if (file_exists(public_path('images/' . $this->image_path))) {
            return asset('images/' . $this->image_path);
        }

        return asset('storage/' . $this->image_path);
    }
}
