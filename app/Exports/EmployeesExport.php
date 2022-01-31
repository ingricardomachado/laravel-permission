<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class EmployeesExport implements FromView 
{
	protected $subscriber;

	function __construct($subscriber) {
        $this->subscriber = $subscriber;
 	}

	public function view(): View
    {   
        return view('xls.xls_employees', [
            'employees' => $this->subscriber->employees()->orderBy('name')->get()
        ]);
    }
}
