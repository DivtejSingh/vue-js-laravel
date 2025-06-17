<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_id',
        'cat_id',
        'sub_cat_id',
        'gst',
        'business_info',
        'plan_id',
        'listedby',
        'listedby_reseller_id',
    ];

    
}
