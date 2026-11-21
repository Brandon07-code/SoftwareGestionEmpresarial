<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Sale extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'sale_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sale_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'sale_details')
            ->withPivot('quantity', 'unit_price', 'subtotal')
            ->withTimestamps();
    }

    public function canBeCancelled(): bool
    {
        return $this->status !== 'anulada' && ! $this->trashed();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['invoice_number', 'client_id', 'sale_date', 'subtotal', 'tax', 'total', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('ventas')
            ->setDescriptionForEvent(fn (string $eventName) => match ($eventName) {
                'created' => "Factura de venta «{$this->invoice_number}» fue creada", 'updated' => "Factura de venta «{$this->invoice_number}» fue actualizada", 'deleted' => "Factura de venta «{$this->invoice_number}» fue anulada", 'restored' => "Factura de venta «{$this->invoice_number}» fue restaurada", default => "Factura de venta «{$this->invoice_number}» fue {$eventName}"
            });
    }
}
