<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
  protected $table = 'service_orders';
  protected $dates = ['date'];

  //*** Relations ***        
  public function customer()
  {
    return $this->belongsTo('App\Models\Customer');
  }

  public function products(){
 
    return $this->belongsToMany('App\Models\Product','service_order_product')
                                  ->withPivot('quantity')
                                  ->withPivot('more_info')
                                  ->withTimestamps();
  }

  public function service()
  {
    return $this->belongsTo('App\Models\Service');
  }

  public function subscriber()
  {
    return $this->belongsTo('App\Models\Subscriber');
  }

  public function user()
  {
    return $this->belongsTo('App\User');
  }

    //*** Accesors ***    
    public function getFolioMaskAttribute(){
        return $this->custom_folio?$this->folio:$this->number;        
    }

}
