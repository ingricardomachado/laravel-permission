<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalesExport implements FromView 
{
	protected $subscriber;

	function __construct($subscriber, $type) {
        $this->subscriber = $subscriber;
        $this->type = $type;
 	}

	public function view(): View
    {   
        return view('xls.xls_sales', [
            'sales' => $this->subscriber->sales()->where('type', $this->type)
                                        ->orderBy('date', 'desc')
                                        ->orderBy('id', 'desc')->get()
        ]);
    }
}
