<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Provider extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_number',
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['document_type', 'document_number', 'name', 'contact_name', 'email', 'phone', 'address', 'active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('proveedores')
            ->setDescriptionForEvent(fn (string $eventName) => "Proveedor «{$this->name}» fue {$eventName}");
    }
}
