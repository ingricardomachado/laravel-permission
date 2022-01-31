<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $table = 'subscribers';
    protected $appends = ['url_logo'];
    
    //*** Relations ***    
    public function categories()
    {
        return $this->hasMany('App\Models\Category');
    }
    
    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\Contact');
    }

    public function customers()
    {
        return $this->hasMany('App\Models\Customer');
    }

    public function employees()
    {
        return $this->hasMany('App\Models\Employee');
    }

    public function invoices()
    {
        return $this->belongsTo('App\Models\Invoice');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Product');
    }
   
    public function receivables()
    {
        return $this->hasMany('App\Models\Receivable');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale');
    }

    public function services()
    {
        return $this->hasMany('App\Models\Service');
    }

    public function service_orders()
    {
        return $this->hasMany('App\Models\ServiceOrder');
    }
    
    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }

    public function suppliers()
    {
        return $this->hasMany('App\Models\Supplier');
    }
        
    public function timezone()
    {
        return $this->belongsTo('App\Models\Timezone');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    

    //*** Methods ***
    
    //*** Accesors ***    
    public function getUrlLogoAttribute(){
        
        return url('subscriber_logo/'.$this->id);
    }

    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                       
        $label=($this->active)?'primary':'danger';

        return "<span class='badge badge-".$label."' style='font-weight:normal'>$this->status_description</span>";
    }

}
