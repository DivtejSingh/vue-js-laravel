<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\DB;
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','mobile_phone','username','user_status','user_role','locale','longitude','latitude',
        'business_feature', 'otp_expires_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

    

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function services(){
        return $this->hasOne(Service::class,'service_user_id','id');
    }
    public function allreview(){
        return $this->hasMany(Review::class,'review_business_id','id')
            ->Join('users','users.id','=', 'reviews.review_user_id')
            ->Join('profiles','profiles.user_id','=', 'users.id')
            ->Select('reviews.*','users.name','profiles.user_avatar', 
            DB::raw("
            CASE 
                WHEN DATE(reviews.created_at) = CURRENT_DATE THEN 'Today'
                WHEN DATEDIFF(CURRENT_DATE, reviews.created_at) = 1 THEN '1 day ago'
                ELSE CONCAT(DATEDIFF(CURRENT_DATE, reviews.created_at), ' days ago') 
            END as days_ago
        "));
    }
    public function jobs(){
        return $this->hasMany(Job::class,'user_id','id')
            ->Join('users','users.id','=', 'jobs.user_id')
            ->Join('profiles','profiles.user_id','=', 'users.id')
            ->Join('cities','cities.id','profiles.city_id','hotdeals')
            ->Join('states','states.id','cities.state_id')
            ->Select('jobs.*','users.name','users.mobile_phone','cities.city','states.state')
            ->Where('jobs.job_status', 1);
    }
    public function allhotdeal(){
        return $this->hasMany(Profile::class,'user_id','id')
            ->Join('hotdeals','hotdeals.business_id','=', 'profiles.id')
            ->Join('users','users.id','=', 'profiles.user_id')
            ->Join('cities','cities.id','=','profiles.city_id')
            ->Join('states','states.id','cities.state_id')
            ->Select('hotdeals.*','profiles.id As bprofile_id','users.id As user_id', 'users.mobile_phone','cities.city','states.state');
    }

    public function allvideo(){
        return $this->hasMany(Video::class,'user_id','id')->Where('videos.video_status', 1);
    }

    public function galleryimage(){
        return $this->hasMany(Galleryimage::class,'user_id','id')
            ->Where('galleryimages.image_type','=', 0);
    }
    public function alltarrif(){
        return $this->hasMany(Tariff::class,'user_id','id');
    }
    public function allsale(){
        $currentDate = date('Y-m-d');
        return $this->hasMany(Sale::class,'user_id','id')
        ->leftjoin('users','users.id','=', 'sales.user_id')
        ->leftjoin('profiles','profiles.user_id','=', 'sales.user_id')
        ->leftjoin('cities','cities.id','=','profiles.city_id')
        ->leftjoin('states','states.id','cities.state_id')
        ->Select('sales.*','cities.city','states.state')
        ->Where('sales.saledate_from', '<=', $currentDate)
        ->Where('sales.saledate_to', '>=', $currentDate)
        ->Where('sales.sale_status', '=', 1)
        ->OrderBy('sales.id','DESC');
    }
    public function allskill(){
        return $this->hasMany(Resellerskill::class,'user_id','id')
        ->join('skills','skills.id','resellerskills.skill_id')
        ->select('resellerskills.*','skills.id as sk_id','skills.skill');
    }
    public function hotdealSingleImage(){
        return $this->hasOne(Hotdealimage::class, 'hotdeal_id')->latestOfMany();
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
 public function sendPasswordResetNotification($token)
    {
        $resetUrl = url("/password/reset/{$token}?email={$this->email}");

        Mail::send(
            'emails.resetpasswordNotification',   // your Blade view
            ['url'  => $resetUrl,                 // data passed to it
             'user' => $this],
            function ($m) {
                // Use your own from-address and name here:
                $m->from('no-reply@bizlx.com', 'Bizlx.com');
                
                // Send to the user:
                $m->to($this->email);
                
                // And *this* is your custom subject:
                $m->subject('Password Reset Request');
            }
        );
    }

    
}
