<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catslider extends Model
{
    use HasFactory;
    protected $fillable = [
        'subcat_id',
        'catslider_status',
    ];
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function info(){
        return $this->hasMany(Business::class,'sub_cat_id','subcat_id')
            ->Join('users','businesses.business_id','=','users.id')
            ->Join('profiles','profiles.user_id','=','users.id')
            ->Join('cities','cities.id','=','profiles.city_id')
            ->Join('states','states.id','=','cities.state_id')
            ->Select('businesses.*','users.name','users.username As business_uname','users.mobile_phone','profiles.city_id','cities.city','profiles.state_id','profiles.user_avatar','states.state')
            ->Where('users.user_status', '1');
    }
    public function hotdeal(){
        $currentDate = date('Y-m-d');
        return $this->hasMany(Business::class,'sub_cat_id','subcat_id')
            ->Join('profiles', 'profiles.user_id','=', 'businesses.business_id')
            ->Join('users','businesses.business_id','=','users.id')
            ->Join('hotdeals', 'hotdeals.business_id','=', 'profiles.id')
            ->Join('cities','cities.id','=','profiles.city_id')
            ->Join('states','states.id','=','cities.state_id')
            ->Select('businesses.*','hotdeals.*','profiles.city_id','cities.city','profiles.state_id','states.state','users.name','users.mobile_phone','users.username As business_uname')
            ->where('hotdeals.date_from', '<=', $currentDate)
            ->where('hotdeals.date_to', '>=', $currentDate)
            ->where('hotdeals.hot_deal_status', '=', 1)
            ->orderBy('hotdeals.id','DESC');

    }

    public function job(){
        return $this->hasMany(Business::class,'sub_cat_id','subcat_id')
            ->Join('users','businesses.business_id','=','users.id')
            ->Join('profiles', 'profiles.user_id','=', 'businesses.business_id')
            ->Join('jobs', 'jobs.user_id','=', 'users.id')
            ->Join('cities','cities.id','=','profiles.city_id')
            ->Join('states','states.id','=','cities.state_id')
            ->Select('businesses.*','users.name','users.mobile_phone','jobs.*','profiles.city_id','profiles.user_avatar','cities.city','profiles.state_id','states.state')
            ->Where('jobs.job_status', 1)
            ->orderBy('jobs.id','DESC');
    }

    public function video(){
        return $this->hasMany(Business::class,'sub_cat_id','subcat_id')
            ->Join('profiles', 'profiles.id','=', 'businesses.business_id')
            ->Join('users','businesses.business_id','=','users.id')
            ->Join('videos','videos.user_id','=','users.id')
            ->Select('businesses.*','videos.video_url','videos.video_title','videos.youtube_id')
            ->Where('videos.video_status', 1)
            ->orderBy('videos.id','DESC');
    }

    public function sales(){
        $currentDate = date('Y-m-d');
        return $this->hasMany(Business::class,'sub_cat_id','subcat_id')
            ->Join('users','businesses.business_id','=','users.id')
            ->Join('profiles', 'profiles.user_id','=', 'businesses.business_id')
            ->Join('sales','sales.user_id','=','users.id')
            ->Join('cities','cities.id','=','profiles.city_id')
            ->Join('states','states.id','=','cities.state_id')
            ->Select('businesses.*','users.name','users.mobile_phone','sales.*','profiles.city_id','cities.city','profiles.state_id','states.state')
            ->where('sales.saledate_from', '<=', $currentDate)
            ->where('sales.saledate_to', '>=', $currentDate)
            ->where('sales.sale_status', '=', 1)
            ->orderBy('sales.id','DESC');
    }
}
