<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use File;
use Carbon\Carbon;
use App\Subscriber;
use App\DailyStatusCount;
use PDF;
use Illuminate\Log;
use Session;
use Auth;
use DB;
use Mail;
use Exception;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;


class TaskController extends Controller
{    
       
    public function task(){
        //        
    }
}
