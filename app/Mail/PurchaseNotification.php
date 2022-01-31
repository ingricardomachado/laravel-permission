<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Purchase;

class PurchaseNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $purchase;
    public $pdf;    
    


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase, $pdf)
    {
        $this->purchase = $purchase;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {        
        return $this->markdown('emails.purchase_notification')
                    ->subject((($this->purchase->type=='O')?'Orden de compra ':'Compra ').' - '.$this->purchase->subscriber->name)
                    ->attachData($this->pdf, (($this->purchase->type=='O')?'Orden de compra ':'Compra ').$this->purchase->folio.'.pdf', [
                    'mime' => 'application/pdf',
                ]);
    }
}
