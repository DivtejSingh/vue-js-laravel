<?php

namespace App\Models;

use File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'user_avatar', 'about', 'address','area', 'country_id', 'state_id', 'city_id', 'profile_status'
    ];
    public function avatar($old_file, $file)
    {
        $destinationPath = '/images/profile_images/';
        $path = public_path().$destinationPath;

        if($old_file){
            Storage::disk('s3')->delete($old_file);
            $real_name = uniqid().'.'.$file->getClientOriginalExtension();
            $img = Image::make($file->getRealPath())->resize('400','400', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
            $final_path = $destinationPath.$real_name;
            Storage::disk('s3')->put($final_path, $img->stream()->__toString());
            return $final_path;
        } else {
            $real_name = uniqid().'.'.$file->getClientOriginalExtension();
            $img = Image::make($file->getRealPath())->resize('400','400', function ($constraint) {$constraint->aspectRatio();$constraint->upsize();});
            $final_path = $destinationPath.$real_name;
            Storage::disk('s3')->put($final_path, $img->stream()->__toString());
            return $final_path;
        }

    }

    public function jobs(){
        return $this->hasMany(Job::class,'user_id','user_id');
    }

    public function sales(){
        return $this->hasMany(Sale::class,'user_id','user_id');
    }

    public function deals(){
        return $this->hasMany(Hotdeal::class,'business_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
