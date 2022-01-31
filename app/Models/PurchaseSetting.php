<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseSetting extends Model
{
  protected $table = 'purchase_settings';

  //*** Relations ***
  public function subscriber()
  {
    return $this->belongsTo('App\Models\Subscriber');
  }  
}
