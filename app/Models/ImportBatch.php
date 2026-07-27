<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportBatch extends Model
{
    protected $fillable = [
        'source',
        'status',
        'total_products',
        'processed_count',
        'failed_count',
        'created_by',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ImportProduct::class);
    }

    public function incrementProcessed(): void
    {
        $this->increment('processed_count');
        $this->refreshStatus();
    }

    public function incrementFailed(): void
    {
        $this->increment('failed_count');
        $this->refreshStatus();
    }

    protected function refreshStatus(): void
    {
        if ($this->processed_count + $this->failed_count >= $this->total_products) {
            $this->update(['status' => 'completed']);
        }
    }
}
