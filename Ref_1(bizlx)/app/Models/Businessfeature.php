<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Businessfeature extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'businessfeature_status',
    ];

    /**
     * Get the main gallery image for the business.
     */
    public function galleryimage()
    {
        return $this->hasOne(Galleryimage::class, 'user_id', 'user_id')
                    ->where('galleryimages.image_type', '=', 0);
    }

    /**
     * Many-to-Many: Featured Business → Cities
     */
    public function cities()
    {
        return $this->belongsToMany(City::class, 'businessfeature_city', 'businessfeature_id', 'city_id');
    }
    
    public function subcategories()
    {
        return $this->belongsToMany(
            Subcategory::class,
            'businessfeature_subcategory',  // your actual pivot table
            'businessfeature_id',
            'subcategory_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
