<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'suppliers';
    protected $appends = ['main_contact'];
    
    //*** Relations ***
    public function contacts()
    {
        return $this->hasMany('App\Models\SupplierContact');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\SupplierDocument');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\State');
    }

    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }

    public function products()
    {
        return $this->hasMany('App\Models\Products');
    }

    public function purchases()
    {
        return $this->hasMany('App\Models\Purchase');
    }

    public function target()
    {
        return $this->belongsTo('App\Models\Target');
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
