<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagecolumn extends Model
{
    use HasFactory;

    public function pages()
    {
        return $this->hasMany(Page::class, 'column_id')->where('status', '1');
    }
    
}
