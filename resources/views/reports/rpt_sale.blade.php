<!DOCTYPE html>
<html lang="en"><head>        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <title>{{ $sale->type=='C'?'Cotización':'Factura' }}</title>
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
                border-width: thin*/;                
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
                border-width: thin*/;                
            }            
            body {
                margin-top: 100px;
                margin-bottom: 110px;
                background: white;
                font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;
                font-size: 11px;
                /*border: solid blue;
                border-width: thin*/;
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
                        <div style="font-size: 16pt"><b>{{ ($sale->type=='C')?'COTIZACIÓN':'FACTURA' }}</b></div>
                    </td>
                    <td width="25%" class="text-center" style="padding-left: 3mm">
                        <div id="folio" class="text-center">
                            <span class="folio">Folio No.</span>
                            <span class="ffolio">{{ $sale->folio }}</span>
                        </div>
                        <div>
                            <span style="font-size:14px">
                                <b>{{ $sale->date->format('d/m/Y') }}</b>
                            </span>
                        </div>
                        @if($sale->type=='C')
                            <div>
                                VENCE {{ $sale->due_date->format('d/m/Y') }}
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
    <table class="table" width="100%" style="font-size: 10px">
        <tbody>
            <tr>
                <td valign="top" width="30%">
                    <b>EMISOR</b><br>
                    <b>Nombre:</b> {{ $sale->subscriber->name }}<br>
                    <b>Nombre Fiscal:</b> {{ $sale->subscriber->bussines_name }}<br>
                    <b>Dirección:</b> {{ $sale->subscriber->address }}<br>
                    <b>Correo:</b> {{ $sale->subscriber->user->email }}<br>
                </td>
                <td valign="top" width="20%">
                    <br>
                    <b>RFC:</b> {{ $sale->subscriber->rfc }}<br>
                    <b>Teléfono:</b> {{ $sale->subscriber->phone }}<br>
                    <b>Elaborado por:</b> {{ $sale->created_by }}
                </td>
                <td valign="top" width="30%">
                    <b>RECEPTOR</b><br>
                    <b>Cliente:</b> {{ ($sale->customer_id)?$sale->customer->name:$sale->prospect }}<br>
                    <b>Nombre Fiscal:</b> {{ ($sale->customer_id)?$sale->customer->bussines_name:'' }}<br>
                    <b>Dirección:</b> {{ $sale->customer->address }}<br>
                </td>
                <td valign="top" width="20%">
                    <br>
                    <b>RFC:</b> {{ ($sale->customer_id)?$sale->customer->rfc:'' }}<br>
                    <b>Teléfono:</b> {{ ($sale->customer_id)?$sale->customer->cell:'' }}<br>
                    <b>Atención:</b> {{ $sale->contact }}<br>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <b>Observaciones:</b> {!! $sale->observations !!}<br>
                    @if($sale->type=='F')
                        <b>Forma de Pago:</b> {{ $sale->way_pay_description }}<br>
                        <b>Método de Pago:</b> {{ $sale->method_pay_description }}<br>
                        <b>Condición de Pago:</b> {{ $sale->condition_pay_description }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <table width="100%" style="font-size: 10px">
        <tbody>
            <tr>
                <th width="10%" class="text-center">Cantidad</th>
                <th width="10%" class="text-left">Código</th>
                <th width="35%" class="text-left">Descripción</th>
                @if($sale->total_discount>0)
                    <th width="10%" class="text-right">Descuento</th>
                @endif
                <th width="20%" class="text-right">Precio Unitario</th>
                <th width="20%" class="text-right">Importe</th>
            </tr>
            @php
                $i=1;
            @endphp
            @foreach($sale->items()->get() as $item)
            @php
                $list_price=$item->unit_price*(1-$item->percent_discount/100);
            @endphp
            <tr>
                <td class="text-center">{{ money_fmt($item->quantity) }}</td>
                <td class="text-left">{{ $item->code }}</td>
                <td class="text-left">{{ $item->description }}</td>
                @if($sale->total_discount>0)
                    <td class="text-right">{{ $item->percent_discount }}%</td>
                @endif
                <td class="text-right">{{ session('coin') }}{{ money_fmt($list_price) }}</td>
                <td class="text-right">{{ session('coin') }}{{ money_fmt($item->sub_total-$item->discount) }}</td>
            </tr>
            @endforeach
            <tr style="font-size: 12px">
                <th colspan="{{ ($sale->total_discount>0)?5:4 }}" class="text-right">
                    <div>SUB TOTAL.:</div>
                    <div>IMPUESTOS.:</div>
                    <div>TOTAL.:</div>
                </th>
                <th colspan="1" class="text-right">
                    <div>{{ session('coin') }}{{ money_fmt($sale->sub_total-$sale->total_discount) }}</div>
                    <div>{{ session('coin') }}{{ money_fmt($sale->total_tax) }}</div>
                    <div>{{ session('coin') }}{{ money_fmt($sale->total) }}</div>
                </th>
            </tr>
        </tbody>
    </table>
    
    <div class="col-sm-12">
        {!! $sale->conditions !!}
    </div>
</body></html>

@php
@endphp