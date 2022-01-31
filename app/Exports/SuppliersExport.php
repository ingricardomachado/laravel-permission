<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SuppliersExport implements FromView 
{
	protected $subscriber;

	function __construct($subscriber) {
        $this->subscriber = $subscriber;
 	}

	public function view(): View
    {   
        return view('xls.xls_suppliers', [
            'suppliers' => $this->subscriber->suppliers()->orderBy('name')->get()
        ]);
    }
}
