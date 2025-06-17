<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Jobcategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_cat_name',
        'job_img_url',
        'job_cat_slug',
        'job_cat_feature',
        'job_cat_sort',
        'job_cat_status',
    ];

    public function add_image($file)
    {
        $destinationPath = '/images/jobcats';
        // $path = public_path().$destinationPath;
        // if(!File::exists($path)) {
        //     File::makeDirectory($path, 0777, true); //make dir
        // }
        // resize & type
        $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
        $resize = Image::make($file)->resize(1280, null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
            }); // resize
        $final_path = $destinationPath.'/'.$real_name;
        Storage::disk('s3')->put($final_path,$resize->stream()->__toString());
        return $final_path;
    }

    // Edit image
    public function edit_image($old_file, $file)
    {
        $destinationPath = '/images/jobcats';
        // $path = public_path().$destinationPath;
        // if(!File::exists($path)) {
        //     File::makeDirectory($path, 0777, true); //make dir
        // }
        // if (!File::exists($path.$old_file)) { // file delete
        //     // unlink(public_path().$old_file);
        // }
        // resize & type
        $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
        $resize = Image::make($file)->resize(1280, null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
            }); // resize
        $final_path = $destinationPath.'/'.$real_name;
        Storage::disk('s3')->put($final_path,$resize->stream()->__toString());
        return $final_path;
    }

}
