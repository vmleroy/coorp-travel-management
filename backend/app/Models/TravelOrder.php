<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    protected $dates = [
        'deleted_at',
    ];
}
