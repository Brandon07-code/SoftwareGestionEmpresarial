<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Client extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_number',
        'name',
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

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['document_type', 'document_number', 'name', 'email', 'phone', 'address', 'active'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges()
            ->useLogName('clientes')
            ->setDescriptionForEvent(fn (string $eventName) => "Cliente «{$this->name}» fue {$eventName}");
    }
}
