<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotdeal extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_id',
        'hot_deal_title',
        'hotdeal_slug',
        'hot_deal_description',
        'price_from',
        'price_to',
        'date_from',
        'date_to',
        'hot_deal_status',
    ];

    public function images(){
        return $this->hasMany(Hotdealimage::class, 'hotdeal_id','id');
    }

    public function hotdealsImages(){
        return $this->hasMany(Hotdealimage::class, 'hotdeal_id');
    }

    public function hotdealSingleImage(){
        return $this->hasOne(Hotdealimage::class, 'hotdeal_id')->latestOfMany();
    }
}
