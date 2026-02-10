<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TravelOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'travel_order';

    protected $fillable = [
        'user_id',
        'destination',
        'departure_date',
        'return_date',
        'status',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    protected $with = ['user'];

    /**
     * Get the user that owns the travel order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
