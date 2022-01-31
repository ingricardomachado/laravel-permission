<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    
    //*** Relations ***   
    public function documents()
    {
        return $this->hasMany('App\Models\ProductDocument');
    }
    
    public function inventory_movements()
    {
        return $this->hasMany('App\Models\InventoryMovement');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function subscriber()
    {
        return $this->belongsTo('App\Models\Subscriber');
    }
        
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }

    public function photos()
    {
        return $this->hasMany('App\Models\ProductPhoto');
    }
    
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }


    //*** Methods ***
    public function update_stock(){
        //inputs
        $this->inputs=$this->inventory_movements()->where('type','I')->sum('quantity');
        //outputs
        $this->outputs=$this->inventory_movements()->where('type','O')->sum('quantity');
        
        $this->stock=$this->initial_stock+$this->inputs-$this->outputs;
        $this->save();
    }

    
    //*** Accesors ***    
    public function getNumberMaskAttribute(){
        return 'P'.$this->number;        
    }
    
    public function getStatusDescriptionAttribute(){
        
        return ($this->active)?'Activo':'Inactivo';
    }

    public function getStatusLabelAttribute(){
                
        $label=($this->active)?'primary':'danger';

        return "<span class='badge badge-".$label."' style='font-weight:normal'>$this->status_description</span>";
    }
}
