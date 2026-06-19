<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalChat extends Model
{
    protected $fillable = [
        'user_id',
        'group_name',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
