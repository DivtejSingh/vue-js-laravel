<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'subcat_name',
        'subcat_slug',
        'subcat_status',
        'subcat_feature',
        'subcat_img_url',
    ];
    // add image
    public function add_image($file)
    {
        $destinationPath = '/images/subcats';
        $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
        $img = Image::make($file)->resize(1280, null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
            }); // resize
        $final_path = $destinationPath.'/'.$real_name;
        Storage::disk('s3')->put($final_path, $img->stream()->__toString());
        return $final_path;
    }

    // Edit image
    public function edit_image($old_file, $file)
    {
        $destinationPath = '/images/subcats/';
        // resize & type
        $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
        $img = Image::make($file)->resize(1280, null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
            }); // resize
        $final_path = $destinationPath.$real_name;
        Storage::disk('s3')->put($final_path, $img->stream()->__toString());
        if($old_file){
            Storage::disk('s3')->delete($old_file);
        }
        return $final_path;
    }
}
