<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'quantity',
        'charge',
        'status',
        'link',
        'comments',
        'start_count',
        'remains',
        'api_order_id',
    ];

    protected $casts = [
        'charge' => 'decimal:5',
        'comments' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
