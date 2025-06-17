<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'state',
        'state_status',
    ];

    public function cities()
    {
        return $this->hasMany(City::Class, 'state_id');
    }
}
