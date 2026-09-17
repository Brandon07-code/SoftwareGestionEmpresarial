<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'active'
    ];

    /**
     * Relación 1:N inversa: Product pertenece a Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
