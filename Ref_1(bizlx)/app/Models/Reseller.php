<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reseller extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'profession_id',
        'skills_id',
        'plan_id',
        'reseller_dob',
        'reseller_gender',
    ];
}
