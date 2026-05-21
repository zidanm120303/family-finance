<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = ['family_id','category_id','month','year','limit_amount'];
    protected $casts = ['limit_amount' => 'decimal:2'];
    public function family(): BelongsTo { return $this->belongsTo(Family::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
}
