<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customers';
    protected $appends = ['main_contact'];    
    
    //*** Relations ***
    public function areas()
    {
        return $this->hasMany('App\Models\Area');
    }
    
    public function assets()
    {
        return $this->hasMany('App\Models\Asset');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\CustomerContact');
    }

    public function contracts()
    {
        return $this->hasMany('App\Models\Contract');
    }
    
    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\CustomerDocument');
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event');
    }
    
    public function parent()
    {
        return $this->belongsTo('App\Models\Customer', 'parent_id');
    }

    public function photos()
    {
        return $this->hasMany('App\Models\Photo');
    }
    
    public function sales()
    {
        return $this->hasMany('App\Models\Sale');
    }
    
    public function service_orders()
    {
        return $this->hasMany('App\Models\ServiceOrder');
    }
    
    public function sites()
    {
        return $this->hasMany('App\Models\Site');
    }
    
    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }

    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }

    public function sucursales()
    {
        return $this->hasMany('App\Models\Customer', 'parent_id');
    }

    public function target()
    {
        return $this->belongsTo('App\Models\Target');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
    public function work_orders()
    {
        return $this->hasMany('App\Models\WorkOrder');
    }
    
    //*** Methods ***
    
    //*** Accesors ***    
    public function getMainContactAttribute(){
        return $this->contacts()->where('main',true)->first();
    }

    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='badge badge-".$label."' style='font-weight:normal'>$this->status_description</span>";
    }

}
