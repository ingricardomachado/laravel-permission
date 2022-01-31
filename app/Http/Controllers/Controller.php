<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Storage;
use Image;
use File;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function upload_file($path, $file)
    {
        $file_name=rand(10000000,9999999999) . '.' . $file->getClientOriginalExtension();
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext == 'jpeg' || $ext == 'jpg' || $ext == 'bmp' || $ext == 'png' || $ext == 'gif'){
            
            $ratio = 1.0;
            $width_config=config('image.img_width', 800);
            $height_config=config('image.img_height', 600);            
            
            $original_image = Image::make($file)->orientate()->save();
            $original_width = $original_image->width();
            $original_height = $original_image->height();

            // FIXME size should be configurable
            if ($original_width > $width_config) {
                $ratio = $width_config / $original_width;
                $width = $original_width * $ratio;
                $height = $original_height * $ratio;
            } else {
                $width = $original_width;
                $height = $original_height;
            }

            if ($height > $height_config) {
                $ratio = $height_config / $original_height;
                $width = $original_width * $ratio;
                $height = $original_height * $ratio;
            }
            $img=Image::make($file)->resize($width, $height)->save();
            Storage::put($path.'/'.$file_name, $img);
            if(config('image.create_thumbnails', true)){
                // create thumb image
                $img_thumb=Image::make($img)->fit(config('image.thumb_img_width', 200), config('image.thumb_img_height', 200))->save();
                Storage::put($path.'/thumbs/'.$file_name, $img_thumb);
            }            
        }else{
            Storage::put($path.'/'.$file_name, File::get($file));
        }
        
        return $file_name;
    }
}
