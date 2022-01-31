<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
  protected $table = 'countries';

  //*** Relations ***        
  public function customers()
  {
    return $this->hasMany('App\Models\Customer');
  }

  public function states()
  {
    return $this->hasMany('App\Models\State');
  }

  public function subscribers()
  {
    return $this->hasMany('App\Models\Subscriber');
  }

  public function timezones()
  {
    return $this->hasMany('App\Models\Timezone');
  }

}
