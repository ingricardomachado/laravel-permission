<!DOCTYPE html>
<html lang="en"><head>        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <title>{{ $purchase->type=='O'?'Orden de Compra':'Compra' }}</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="{{ $root_public }}/vendor/adminlte/dist/css/adminlte.min.css">
        
        <style type="text/css">  
            header {
                position: fixed;
                top: -20px;
                left: 0px;
                right: 0px;
                height: 120px;
                /*border: solid red;
                border-width: thin;*/                
            }
            footer {
                position: fixed; 
                bottom: -20px; 
                left: 0px; 
                right: 0px;
                height: 120px;
                text-align:center;
                font-size: 9px;
                /*border: solid red;
                border-width: thin;*/                
            }            
            body {
                margin-top: 100px;
                margin-bottom: 110px;
                background: white;
                font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;
                font-size: 11px;
                /*border: solid blue;
                border-width: thin;*/
            }
            small {
                font-size: 10px;}
            @page {
                margin-top: 3.0em;
                margin-right: 3.0em;
                margin-left: 3.0em;
                margin-bottom: 3.0em;
            }
            .saltopagina{page-break-after:always;
            }
            .text-center {
                text-align: center;
            }
            .text-left {
                text-align: left;
            }
            .text-right {
                text-align: right;
            }
#folio {
  padding: 5px;
  margin-bottom: 4px;
  border: 1px solid #6E0000;
  border-radius: 4px; 
  width: 88%;
  font-size: 11pt;
}
.folio {
  font-weight: 700;
  color: #6E0000;
}
.ffolio {
  color: #6E0000;
}
</style>
</head><body>
    <header>
        <table width="100%">
            <tbody>
                <tr>
                    <td width="25%">
                        <div><img alt="image" style="max-width:150px;max-height:100px" src="{{ $logo }}"/></div>
                    </td>
                    <td width="50%" class="text-center">
                        <div style="font-size:13px"><b>{{ $subscriber->bussines_name }}</b></div>
                        @if($subscriber->rfc)
                            <div style="font-size:12px">RFC. {{ $subscriber->rfc }}</div>
                        <div>
                        @endif
                        {{ $subscriber->address }} {{ ($subscriber->state_id)?$subscriber->state->name:''}}, {{ $subscriber->city}}
                        </div>
                    </td>
                    <td width="25%" class="text-center" style="padding-left: 3mm">
                        <div id="folio" class="text-center">
                            <span class="folio">{{ ($purchase->type=='O')?'Orden de Compra':'Compra' }} No.</span>
                            <span class="ffolio">{{ $purchase->folio }}</span>
                        </div>
                        <div>
                            <span style="font-size:14px">
                                <b>{{ $purchase->date->format('d/m/Y') }}</b>
                            </span>
                        </div>
                        @if($purchase->type=='O')
                            <div>
                                VENCE {{ $purchase->due_date->format('d/m/Y') }}
                            </div>
                        @endif
                        @if($setting->show_coin_name)
                            <div>
                                <b>Moneda</b> {{ session('coin_name') }}
                            </div>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </header>
    <footer>
        <div class="col-sm-12 text-center">
            <div><img src="{{ $sello }}" style="max-width:110px;max-height:110px;"></div>
            <script type="text/php">
                $y = $pdf->get_height() - 24;
                $x = $pdf->get_width() - 100;
                $font_size = 8;         
                $text_left = 'Página: {PAGE_NUM} / {PAGE_COUNT}';
                $pdf->page_text($x, $y, $text_left, null, $font_size);
            </script>
        </div>
    </footer>
    <!-- Body -->
    <table class="table" width="100%">
        <tbody>
            <tr>
                <td valign="top" width="50%">
                    <b>EMISOR</b><br> 
                    <b>Nombre:</b> {{ $purchase->subscriber->name }}<br>
                    <b>Razón Social:</b> {{ $purchase->subscriber->bussines_name }}<br>
                    <b>RFC:</b> {{ $purchase->subscriber->rfc }}<br>
                    <b>Teléfono:</b> {{ $purchase->subscriber->phone }}<br>
                    <b>Correo:</b> {{ $purchase->subscriber->user->email }}<br>
                    <b>Elaborado por:</b> {{ $purchase->created_by }}
                </td>
                <td valign="top" width="50%">
                    <b>RECEPTOR</b><br> 
                    <b>Proveedor:</b> {{ ($purchase->supplier_id)?$purchase->supplier->name:$purchase->prospect }}<br>
                    <b>Razón Social:</b> {{ ($purchase->supplier_id)?$purchase->supplier->bussines_name:'' }}<br>
                    <b>RFC:</b> {{ ($purchase->supplier_id)?$purchase->supplier->rfc:'' }}<br>
                    <b>Atención:</b> {{ $purchase->contact }}<br>
                    <b>Dirección:</b> {{ ($purchase->supplier_id)?$purchase->supplier->address:'' }}<br>
                    <b>Teléfono:</b> {{ ($purchase->supplier_id)?$purchase->supplier->cell:'' }}<br>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {!! $purchase->observations !!}
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table" width="100%" style="font-size: 10px">
        <tbody>
            <tr>
                <th width="5%">Artículo</th>
                <th width="10%">Cantidad</th>
                <th width="60%">Descripción</th>
                <th width="25%" class="text-right">Sub total</th>
            </tr>
            @foreach($purchase->items()->get() as $item)                    
            <tr>
                <td>1</td>
                <td>{{ money_fmt($item->quantity) }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ session('coin') }} {{ money_fmt($item->sub_total) }}</td>
            </tr>
            @endforeach
            <tr style="font-size: 12px">
                <th colspan="3" class="text-right">
                    <div>SUB TOTAL.:</div>
                    @if($purchase->total_discount>0)
                    <div>DESCUENTO.:</div>
                    @endif
                    <div>IVA.:</div>
                    <div>TOTAL.:</div>
                </th>
                <th colspan="1" class="text-right">
                    <div>{{ session('coin') }} {{ money_fmt($purchase->sub_total) }}</div>
                    @if($purchase->total_discount>0)
                        <div>{{ session('coin') }} {{ money_fmt($purchase->total_discount) }}</div>
                    @endif
                    <div>{{ session('coin') }} {{ money_fmt($purchase->total_tax) }}</div>
                    <div>{{ session('coin') }} {{ money_fmt($purchase->total) }}</div>
                </th>
            </tr>
        </tbody>
    </table>
    
    <div class="col-sm-12">
        {!! $purchase->conditions !!}
    </div>
</body></html>

@php
@endphp