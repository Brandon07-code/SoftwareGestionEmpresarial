<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CodigoOtp extends Model
{
    use HasFactory;

    protected $table = 'codigo_otps';

    protected $fillable = [
        'user_id',
        'codigo',
        'tipo',
        'expira_at',
        'usado',
    ];

    protected $casts = [
        'expira_at' => 'datetime',
        'usado' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return !$this->usado && $this->expira_at->isFuture();
    }
}
