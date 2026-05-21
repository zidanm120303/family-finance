<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = ['family_id','user_id','category_id','wallet_id','transaction_code','type','amount','title','description','transaction_date','attachment','payment_method','status'];
    protected $casts = ['amount' => 'decimal:2', 'transaction_date' => 'date'];
    public function family(): BelongsTo { return $this->belongsTo(Family::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function wallet(): BelongsTo { return $this->belongsTo(Wallet::class); }
    public function histories(): HasMany { return $this->hasMany(TransactionHistory::class); }
}
