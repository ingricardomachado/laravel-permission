<?php

namespace App\Models;
use DB;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
  protected $connection= 'brique';
  protected $table = 'projectupdates';
  protected $primaryKey = '_id';
  public $timestamps = false;

  //*** Relations ***        

}
