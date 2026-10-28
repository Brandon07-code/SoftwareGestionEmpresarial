<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Product extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'name', 'description', 'price', 'stock', 'category_id', 'active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class)->orderByDesc('id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'price', 'stock', 'category_id', 'active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('productos')
            ->setDescriptionForEvent(fn (string $eventName) => "Producto «{$this->name}» fue {$eventName}");
    }
}
