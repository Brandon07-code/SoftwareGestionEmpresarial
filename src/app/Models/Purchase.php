<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Purchase extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'purchase_number',
        'provider_id',
        'user_id',
        'purchase_date',
        'provider_invoice',
        'subtotal',
        'tax',
        'total',
        'status',
        'received_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'received_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function canBeReceived(): bool
    {
        return in_array($this->status, ['pendiente', 'parcial']);
    }

    public function canBeCancelled(): bool
    {
        return $this->status === 'pendiente';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['purchase_number', 'provider_id', 'purchase_date', 'subtotal', 'tax', 'total', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('compras')
            ->setDescriptionForEvent(fn (string $eventName) => "Orden de compra «{$this->purchase_number}» fue {$eventName}");
    }
}
