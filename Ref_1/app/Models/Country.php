<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'country_status',
    ];

    public function states()
    {
        return $this->hasMany(State::Class, 'country_id');
    }
}
