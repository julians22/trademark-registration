<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        // Define fillable attributes here
        'name',
        'email',
        'phone',
        'company',
        'whatsapp',
        'wechat',
    ];

    // have more than one registration details
    public function details()
    {
        return $this->hasMany(RegistrationDetail::class);
    }

}
