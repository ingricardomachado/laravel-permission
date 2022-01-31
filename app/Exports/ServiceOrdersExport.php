<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ServiceOrdersExport implements FromView 
{
	protected $subscriber;

	function __construct($subscriber) {
        $this->subscriber = $subscriber;
 	}

	public function view(): View
    {   
        return view('xls.xls_service_orders', [
            'service_orders' => $this->subscriber->service_orders()->orderBy('date', 'desc')->get()
        ]);
    }
}
