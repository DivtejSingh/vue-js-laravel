<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagecontent extends Model
{
    use HasFactory;

    // Define the table name if it's not the plural form of the model
    protected $table = 'pagecontents'; // or 'page_contents' if you're using snake_case naming

    // Specify fillable fields for mass assignment
    protected $fillable = [
        'page_id',
        'content',
        'status'
    ];

    // (Optional) Disable timestamps if the table does not have created_at and updated_at
    public $timestamps = false;

    // (Optional) Define relationships, e.g., belongsTo Page model
    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}
