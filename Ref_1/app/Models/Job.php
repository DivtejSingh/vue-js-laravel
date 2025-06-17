<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\Count;

class Job extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_cat_id',
        'user_id',
        'city_id',
        'job_title',
        'job_slug',
        'job_type',
        'job_qualification',
        'job_description',
        'salary_from',
        'min_exp',
        'job_status',
    ];
}
