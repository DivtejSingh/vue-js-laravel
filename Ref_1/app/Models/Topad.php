<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Topad extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_id',
        'ad_img_url',
        'ad_status',
    ];

    public function add_image($file)
    {
        $destinationPath = '/images/topads/';
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
        $destinationPath = '/images/topads/';
        $path = public_path().$destinationPath;

        // resize & type
        $real_name = uniqid().'.'.$file->getClientOriginalExtension(); // type change
        $resize = Image::make($file)->resize(1280, null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
            }); // resize
        $final_path = $destinationPath.$real_name;
        Storage::disk('s3')->put($final_path,$resize->stream()->__toString());
        if($old_file){
            Storage::disk('s3')->delete($old_file);
        }
        return $final_path;
    }
}
