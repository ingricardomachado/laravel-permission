<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Notifications\ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use HasRoles;

    protected $appends = ['url_avatar', 'url_signature'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    //***Relations****
    public function service_orders()
    {
        return $this->hasMany('App\Models\ServiceOrder');
    }


    //***Methods****
    public function isSuperAdmin(){

        return (session()->get('role')=='SAM')?true:false;            
    }

    public function isAdmin(){
   
        return (session()->get('role')=='ADM')?true:false;
    }

    public function isCustomer(){
   
        return (session()->get('role')=='CLI')?true:false;
    }

    public function isTechnician(){
   
        return (session()->get('role')=='TEC')?true:false;
    }

    //*** Accesors ***    
    public function getUrlAvatarAttribute(){
        
        return url('user_avatar/'.$this->id);
    }
    
    public function getUrlSignatureAttribute(){
        
        return ($this->signature)?url('user_signature/'.$this->id):'';
    }

    public function getRoleAttribute(){
        
        if($this->hasRole('SAM')){
            return "SAM";
        }elseif($this->hasRole('CLI'.$this->subscriber_id)){
            return "CLI";
        }else if($this->hasRole('ADM'.$this->subscriber_id)){
            return "ADM";
        }else if($this->hasRole('AOP'.$this->subscriber_id)){
            return "AOP";   
        }else if($this->hasRole('AXA'.$this->subscriber_id)){
            return "AXA";   
        }else if($this->hasRole('CON'.$this->subscriber_id)){
            return "CON";   
        }else if($this->hasRole('COM'.$this->subscriber_id)){
            return "COM";   
        }else if($this->hasRole('ALM'.$this->subscriber_id)){
            return "ALM";   
        }else if($this->hasRole('VEN'.$this->subscriber_id)){
            return "VEN";   
        }else if($this->hasRole('VEF'.$this->subscriber_id)){
            return "VEF";   
        }else if($this->hasRole('MEN'.$this->subscriber_id)){
            return "MEN";   
        }else if($this->hasRole('TEC'.$this->subscriber_id)){
            return "TEC";   
        }else if($this->hasRole('TEF'.$this->subscriber_id)){
            return "TEF";   
        }else{
            return "";   
        }
    }

    public function getRoleDescriptionAttribute(){
        
        if($this->hasRole('SAM')){
            return "Super Administrador";
        }else if($this->hasRole('CLI'.$this->subscriber_id)){
            return "Cliente";
        }else if($this->hasRole('ADM'.$this->subscriber_id)){
            return "Administrador";
        }else if($this->hasRole('AOP'.$this->subscriber_id)){
            return "Administrador Operativo";   
        }else if($this->hasRole('AXA'.$this->subscriber_id)){
            return "Auxiliar Administrativo";   
        }else if($this->hasRole('CON'.$this->subscriber_id)){
            return "Contador";   
        }else if($this->hasRole('COM'.$this->subscriber_id)){
            return "Comprador";   
        }else if($this->hasRole('ALM'.$this->subscriber_id)){
            return "Almacenista";   
        }else if($this->hasRole('VEN'.$this->subscriber_id)){
            return "Vendedor";   
        }else if($this->hasRole('VEF'.$this->subscriber_id)){
            return "Vendedor Full";   
        }else if($this->hasRole('MEN'.$this->subscriber_id)){
            return "Mensajero";   
        }else if($this->hasRole('TEC'.$this->subscriber_id)){
            return "Técnico";   
        }else if($this->hasRole('TEF'.$this->subscriber_id)){
            return "Técnico Full";   
        }else{
            return "sin rol";   
        }
    }
}
