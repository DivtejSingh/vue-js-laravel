<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saleimage extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id',
        'sale_img_url',
        'image_status',
    ];
}
