<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Models\Picture\Picture;
use App\User;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\AssetPhoto;
use App\Models\Photo;
use File;
use Image;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Storage;




class ImgController extends Controller
{
   
    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showUserAvatar($id)
    {
        $user = User::findOrFail($id);
        if($user->avatar){
            if($user->subscriber_id){
                $picture = Image::make(Storage::get($user->subscriber_id.'/users/'.$user->avatar));
            }else{
                $picture = Image::make(Storage::get('/users/'.$user->avatar));
            }
        }else{
            $picture = Image::make(public_path().'/img/avatar_default.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }
    
    public function showUserSignature($id)
    {
        $user = User::findOrFail($id);
        if($user->subscriber_id){
            $picture = Image::make(Storage::get($user->subscriber_id.'/users/'.$user->signature));
        }else{
            $picture = Image::make(Storage::get('/users/'.$user->signature));
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showAppLogo()
    {
        $setting = Setting::first();
        if($setting->logo){
            $picture = Image::make(Storage::get($setting->logo));
        }else{
            $picture = Image::make(public_path().'/img/no_image_available.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }


    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showSubscriberLogo($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        if($subscriber->logo!=null){
            $picture = Image::make(Storage::get($subscriber->id.'/'.$subscriber->logo));
        }else{
            $picture = Image::make(public_path().'/img/no_image_available.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

    /*
     * Extracts picture's data from DB and makes an image 
    */ 
    public function showSubscriberStamp($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        if($subscriber->logo!=null){
            $picture = Image::make(Storage::get($subscriber->id.'/'.$subscriber->stamp));
        }else{
            $picture = Image::make(public_path().'/img/no_image_available.png');
        }
        $response = Response::make($picture->encode('jpg'));
        $response->header('Content-Type', 'image/jpeg');

        return $response;
    }

}
?>