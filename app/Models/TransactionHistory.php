<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionHistory extends Model
{
    public $timestamps = false;
    protected $fillable = ['transaction_id','user_id','action','old_data','new_data','note','created_at'];
    protected $casts = ['old_data' => 'array', 'new_data' => 'array', 'created_at' => 'datetime'];
    public function transaction(): BelongsTo { return $this->belongsTo(Transaction::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
