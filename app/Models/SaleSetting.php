<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleSetting extends Model
{
  protected $table = 'sale_settings';

  //*** Relations ***
  public function subscriber()
  {
    return $this->belongsTo('App\Models\Subscriber');
  }  
}
