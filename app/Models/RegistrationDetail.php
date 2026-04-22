<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationDetail extends Model
{
    protected $fillable = [
        // Define fillable attributes here
        'registration_id',
        'word_marks',
        'logo',
        'classifications',
        'goods_services',
        'currency',
        'trademark_administration',
        'countries',
    ];

    // belong to one registration
    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
