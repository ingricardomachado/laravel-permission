<!DOCTYPE html>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Orden de Servicio</title>
<style type="text/css">
            @page {
                margin-top: 0em;
                margin-right: 2em;
                margin-left: 2em;
                margin-bottom: 0.5em;                
            }
            header {
                position: fixed;
                top: 5px;
                height: 120px;
                left: 0px;
                right: 0px;
                /*border: solid red;
                border-width: thin;*/
            }
            footer {
                background-color: #003333;
                position: fixed; 
                bottom: 5px; 
                height: 80px;
                left: 0px; 
                right: 0px;
                text-align:center;
                /*border: solid red;
                border-width: thin;*/
            }            
            body {
                margin-top: 130px;
                margin-bottom: 87px;
                font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;
                font-size: 10pt;
                /*border: solid blue;
                border-width: thin;*/
            }
            small {
                font-size: smaller;
            }    
            .saltopagina{
              page-break-after:always;
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

/* Encabezado */
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
.er {
  border: 1px solid rgba(110,0,0,1.00);
  border-radius: 4px;
  padding: 1px;
  margin: 4px 0 0 0;
}
.eret {
  font-size: 10pt;
  font-weight: 700;
  color: #6E0000;
  margin: 4px 4px 4px 4px;
}
.ertel {
  font-size: 10pt;
  font-weight: 400;
  margin: 4px 4px 4px 4px;  
}

/* Datos Cliente */
#datoc {
  border-top: 8px solid #003333;
  margin-top: 2px;
  padding: 0 0 0 0;
}
.etiqueta {
  font-size: 10pt;
  font-weight: 700;
  color: #003333;
  background-color: rgba(255,255,255,0.3);
  margin-top: 4px;
}

/* Tiempos */
#tiempos {
  border-top: 8px solid #003333;
  margin-top: 2px;
  padding: 4px 0 4px 0;
}

/* Datos */
tbody > tr > th {
  background-color: #003333;
  color: #fdfdfd;
  padding: 2px 0 2px 0;
}
.tcols {
  padding: 4px;
  font-size: 9pt;
  font-weight: 400;
}
#datos > tbody > tr > td {
  padding: 4px;
  font-size: 8.5pt;
}

/* Actividades */
#actividades {
  border-top: 8px solid #003333;
  margin-top: 2px;
  padding: 4px 0 4px 0;
}

/* Contactos */
#contactos {
  border-top: 8px solid #003333;
  margin-top: 2px;
  padding: 4px 0 4px 0;
}

/* Pie */
.localiza {
  font-weight: 700;
  color: #ffffff;
  margin: 8px 0 8px 0;
  text-align: center;
}
.licencia {
  font-size: 10pt;
  font-weight: 400;
  text-align: center;
  color: #ffffff;
  padding: 0pt 0 2pt 0;
}
.web {
  font-weight: 700;
  color: #ffffff;
  text-align: center;
}

/* Bootstrap 3.3.7 */
.row {
  margin-right: -15px;
  margin-left: -15px;
}
.row:before,
.row:after {
  display: table;
  content: " ";
}
.row:after {
  clear: both;
}
.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
  position: relative;
  min-height: 1px;
  padding-right: 15px;
  padding-left: 15px;
}
.col-xs-1, .col-xs-2, .col-xs-3, .col-xs-4, .col-xs-5, .col-xs-6, .col-xs-7, .col-xs-8, .col-xs-9, .col-xs-10, .col-xs-11, .col-xs-12 {
  float: left;
}
.col-xs-6 {
  width: 50%;
}
.text-center {
  text-align: center;
}
img {
  vertical-align: middle;
}
.img-responsive,
.thumbnail > img,
.thumbnail a > img,
.carousel-inner > .item > img,
.carousel-inner > .item > a > img {
  display: block;
  max-width: 100%;
  height: auto;
}
.img-rounded {
  border-radius: 6px;
}
.img-thumbnail {
  display: inline-block;
  max-width: 100%;
  height: auto;
  padding: 4px;
  line-height: 1.42857143;
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
  -webkit-transition: all .2s ease-in-out;
       -o-transition: all .2s ease-in-out;
          transition: all .2s ease-in-out;
}
.img-circle {
  border-radius: 50%;
}
.thumbnail .caption {
  padding: 9px;
  color: #333;
}
</style>
</head><body>                       
  <header>
    <table width="100%" border="0">
        <tbody>
            <tr>
                <td width="25%">
                    @if(true)
                      <div><img id="logo" src="{{ $logo }}" alt="logo" style="max-width:150px;max-height:110px;"></div>
                    @endif
                </td>
                <td width="55%">
                    <div class="text-center" style="font-size:18px">
                      <b>{{ $service_order->subscriber->bussines_name }}</b><br>
                      Orden de Servicio
                    </div>
                </td>
                <td width="20%">
                    <div id="folio">
                        <span class="folio">Folio</span>
                        <span class="ffolio">{{ $service_order->folio_mask }}</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>        
  </header>
  <footer>
    <table width="100%" border="0">
      <tbody>
        <tr>
          <td>
            <div class="localiza">{{ $service_order->subscriber->address }}</div>
            <div class="web">Tel. {{ $service_order->subscriber->phone}} | {{ $service_order->subscriber->email }}</div>
          </td>
        </tr>
      </tbody>
    </table>
  </footer>
<!-- Datos Cliente -->
  <table id="datoc" width="100%" border="0" cellspacing="2">
    <tbody>
      <tr>
        <td width="25%" valign="top">
          <div class="etiqueta">Cliente</div>
          {{ $service_order->customer->name }}
        </td>
        <td width="25%" valign="top">
          <div class="etiqueta">Contacto</div>
          {{ $service_order->customer->contact }}
        </td>
        <td width="30%" valign="top">
          <div class="etiqueta">Correo</div>
          {{ $service_order->customer->email }}
        </td>
        <td width="20%" valign="top">
          <div class="etiqueta">Teléfono</div>
          {{ $service_order->customer->cell }}
        </td>
      </tr>
      <tr>
        <td colspan="4" valign="top">
          <div class="etiqueta">Dirección</div>
          {{ $service_order->customer->address }}
        </td>
      </tr>
    </tbody>
  </table>
      
<!-- Tiempos -->
  <table id="tiempos" width="100%" border="0" cellspacing="3">
    <tbody>
      <tr>
        <td width="50%" valign="top">
          <div class="etiqueta">Servicio</div>
          {{ $service_order->service->name }}
        </td>
        <td width="50%" valign="top">
          <div class="etiqueta">Fecha</div>
          {{ $service_order->date->format('d/m/Y H:i') }}
        </td>
      </tr>
    </tbody>
  </table>

<!-- Tabla de datos -->
  <table id="datos" width="100%" border="0" cellspacing="0">
    <tbody>
      <tr>
        <th scope="col" class="tcols text-left">Código fabricante</th>
        <th scope="col" class="tcols text-left">Producto</th>
        <th scope="col" class="tcols text-center">Cantidad</th>
        <th scope="col" class="tcols text-left">Mas información</th>
      </tr>
        @foreach($service_order->products()->get() as $product)
          <tr>
            <td class="text-left">{{ $product->code }}</td>
            <td class="text-left">{{ $product->name }}</td>
            <td class="text-center">{{ $product->pivot->quantity }}</td>
            <td class="text-left">{{ $product->pivot->more_info }}</td>
          </tr>
        @endforeach          
    </tbody>
  </table>

<!-- Actividades -->
  <table id="actividades" width="100%" border="0" cellspacing="0" cellpadding="0" style="table-layout:fixed;">
    <tbody>
      <tr>
        <td colspan="2">
          <div class="etiqueta">Actividades Realizadas</div>
          <span style="font-size:9pt">{!! nl2br($service_order->activities) !!}</span>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <div class="etiqueta">Recomendaciones</div>
          <span style="font-size:9pt">{!! nl2br($service_order->recomendations) !!}</span>
        </td>
      </tr>
    </tbody>
  </table>
    
<!-- Contactos 2 -->
<table id="contactos" width="100%" border="0" cellspacing="4" cellpadding="4">
    <tbody>
      <tr>
        <td width="35%" style="padding-left:40px">
            <div class="etiqueta">Correo técnico</div>
            {{ $service_order->user->email }}
        </td>
        <td width="30%"></td>
        <td width="35%">
            <div class="etiqueta">Correo cliente</div>
            {{ $service_order->contact_email }}
        </td>
      </tr>
      <tr>
        <td style="padding-left:40px">
          <div class="etiqueta">{{ $service_order->user->name }}</div>
          <div class="firma">
            <img src="{{ $user_signature }}" style="max-height:130px; max-width:130px;">
          </div>
        </td>
        <td align="center">
          @if(true)
            <img id="stamp" src="{{ $stamp }}" alt="stamp" style="max-width:130px;height:auto;">
          @endif
        </td>
        <td>
          <div class="etiqueta">{{ $service_order->contact }}</div>
          <div class="firma">
              <img src="{{ $contact_signature }}" style="max-height:130px; max-width:130px;">
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  @if($photos->count()>0)
  <div>
    <div class="saltopagina"></div>
    <div class="etiqueta text-center" style="font-size:14px"><b>Reporte Fotográfico</b></div>
    <!-- Photos -->
    @foreach($photos->chunk(2) as $items)
      <div class="row" style="margin-left:20px;margin-right:20px;margin-top:30px">
        @foreach($items as $photo)
          <div class="col-xs-6">
            <div><img class="img-thumbnail" style="max-height:300px;max-width:300px" src="{{ 'data:image/png;base64, '.base64_encode(Storage::get($photo->customer->subscriber_id.'/photos/thumbs/'.$photo->file)) }}"/></div>
            <div style="font-size:10pt">{{ $photo->note }}</div>
          </div>
        @endforeach
      </div>
    @endforeach
    <!-- /Photos -->
  </div>
  @endif
</body></html>