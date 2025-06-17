<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'business_id',
        'wishlist_type',
        'services_id',
        'wishlist_status',
    ];

    public function hotdealsImages(){
        return $this->hasOne(Hotdealimage::class, 'hotdeal_id')->latestOfMany();
    }
}
