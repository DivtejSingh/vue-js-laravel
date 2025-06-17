<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $fillable = [
        'review_user_id',
        'review_business_id',
        'rating',
        'review_text',
    ];

    public function allReviewImages(){
        return $this->hasMany(ReviewImage::class, 'review_id')->select('review_images.id', 'review_images.review_id', 'review_images.review_image_url');
    }
}
