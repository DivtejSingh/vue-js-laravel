<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use File;

class Citydeal extends Model
{
    use HasFactory;
    protected $fillable = [
        'city_id',
        'city_img_url',
        'citydeal_status',
    ];
    public function add_image($file)
    {
        $destinationPath = '/images/citydeal/';
        // resize & type
        $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
        $resize = Image::make($file)->resize(1280, null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
            }); // resize
        $final_path = $destinationPath.$real_name;
        Storage::disk('s3')->put($final_path,$resize->stream()->__toString());
        return $final_path;
    }
    // Edit image
    public function edit_image($old_file, $file)
    {
        $destinationPath = '/images/citydeal/';
        $path = public_path().$destinationPath;
        if(!File::exists($path)) {
            File::makeDirectory($path, 0777, true); //make dir
        }
        if (File::exists($path.$old_file)) { // file delete
            unlink(public_path().$old_file);
        }
        // resize & type
        $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
        $resize = Image::make($file)->resize(1280, null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
            }); // resize
        $final_path = $destinationPath.$real_name;
        Storage::disk('s3')->put($final_path,$resize->stream()->__toString());
        if($old_file){
            Storage::disk('s3')->delete($destinationPath.$old_file);
        }
        return $final_path;
    }
}
