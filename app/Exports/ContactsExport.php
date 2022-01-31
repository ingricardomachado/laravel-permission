<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ContactsExport implements FromView 
{
	protected $subscriber;

	function __construct($subscriber) {
        $this->subscriber = $subscriber;
 	}

	public function view(): View
    {   
        return view('xls.xls_contacts', [
            'contacts' => $this->subscriber->contacts()->orderBy('full_name')->get()
        ]);
    }
}
