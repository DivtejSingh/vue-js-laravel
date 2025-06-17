<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotdealimage extends Model
{
    use HasFactory;
    protected $fillable = [
        'hotdeal_id',
        'hotdeal_img_url',
        'image_status',
    ];
}
