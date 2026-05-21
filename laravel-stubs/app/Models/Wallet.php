<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = ['family_id','wallet_name','balance','type','account_number'];
    protected $casts = ['balance' => 'decimal:2'];
    public function family(): BelongsTo { return $this->belongsTo(Family::class); }
    public function transactions(): HasMany { return $this->hasMany(Transaction::class); }
}
