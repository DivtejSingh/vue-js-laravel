<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;

class Galleryimage extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'image_type',
        'image_url',
        'image_title',
        'image_description',
        'image_status',
    ];
}
