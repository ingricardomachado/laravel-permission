<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Sale;

class SaleNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;
    public $pdf;    
    


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Sale $sale, $pdf)
    {
        $this->sale = $sale;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {        
        return $this->markdown('emails.sale_notification')
                    ->subject((($this->sale->type=='C')?'Cotización ':'Factura ').' - '.$this->sale->subscriber->name)
                    ->attachData($this->pdf, (($this->sale->type=='C')?'Cotización ':'Factura ').$this->sale->folio.'.pdf', [
                    'mime' => 'application/pdf',
                ]);
    }
}
