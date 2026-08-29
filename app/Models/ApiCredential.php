<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
    protected $fillable = [
        'provider_name',
        'label',
        'masked_value',
        'encrypted_payload',
        'status',
        'last_tested_at',
        'last_used_at',
    ];

    protected $hidden = [
        'encrypted_payload', // NEVER expose encrypted secret in arrays or JSON!
    ];

    protected $casts = [
        'last_tested_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];
}
