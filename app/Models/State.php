<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class State extends Model
{
  protected $table = 'states';

  //*** Relations ***        
  public function country()
  {
    return $this->belongsTo('App\Models\Country');
  }

}
