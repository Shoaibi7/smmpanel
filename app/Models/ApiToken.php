<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = ['user_id', 'token', 'name', 'last_used_at'];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(User $user, string $name = 'Default'): self
    {
        return self::create([
            'user_id' => $user->id,
            'token'   => Str::random(64),
            'name'    => $name,
        ]);
    }
}
