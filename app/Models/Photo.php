<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';
    
    //*** Relations ***
    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }
    
    
    //*** Methods ***
    
    //*** Accesors ***    

}
