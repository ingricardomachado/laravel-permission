<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timezone extends Model
{
    protected $table = 'timezones';
    
    //*** Relations ***
    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    public function subscribers()
    {
        return $this->hasMany('App\Subscriber');
    }
}
