<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Request;
use Intervention\Image\Facades\Image;
use File;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_name',
        'cat_img_url',
        'cat_slug',
        'cat_feature',
        'cat_sort',
        'cat_status',
        ];

    public function subcats()
    {
        return $this->hasMany(Subcategory::Class, 'cat_id')->orderBy('subcat_name','ASC');
    }

    // add image
    public function add_image($file)
    {
        $destinationPath = '/images/categories';
             // resize & type
        $real_name = time().hexdec(rand(11111,99999)) . '.' . "png"; // type change
        $resize = Image::make($file)->resize(1280, null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
            }); // resize
        $final_path = $destinationPath.'/'.$real_name;
        Storage::disk('s3')->put($final_path, $resize->stream()->__toString());
        return $final_path;
    }

    // Edit image
    public function edit_image($old_file, $file)
    {
        $destinationPath = '/images/categories/';
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

    public function delete_image($filename)
    {
        $path = public_path('images/categories/' . $filename);
    
        if (file_exists($path)) {
            unlink($path);
        }
    }

}
