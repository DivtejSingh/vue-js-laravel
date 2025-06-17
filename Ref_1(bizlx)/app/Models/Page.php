<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    // Specify the table name only if it's different from 'pages'
    // protected $table = 'pages';

    // Allow mass assignment for specific columns
    protected $fillable = [
        'column_id',
        'page_name',
        'page_link',
        'status',
        'page_status'
    ];

    // Relationship: One Page has one Pagecontent
    public function content()
    {
        return $this->hasOne(Pagecontent::class, 'page_id');
    }
}
