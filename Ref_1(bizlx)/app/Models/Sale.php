<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'sale_imageurl',
        'sale_title',
        'sale_slug',
        'sale_description',
        'normal_price',
        'sale_price',
        'saledate_from',
        'saledate_to',
        'sale_status',
    ];

    public function salesImages(){
        return $this->hasMany(Saleimage::class, 'sale_id');
    }
}
