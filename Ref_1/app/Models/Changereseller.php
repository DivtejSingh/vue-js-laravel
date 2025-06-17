<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Changereseller extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id', 'old_reseller_id', 'new_reseller_id', 'reason_text',
    ];
}
