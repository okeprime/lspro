<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'form_type', 'section', 'label', 'name', 'type', 'options', 'is_required', 'order_index'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];
}
