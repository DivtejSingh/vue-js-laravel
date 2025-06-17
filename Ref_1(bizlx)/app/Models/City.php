<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'city',
        'city_status',
    ];

    public function jobcount(){
        return $this->hasMany(Job::class,'city_id','id');
    }
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
