@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.ICheck', true)
@section('plugins.Moment', true)
@section('plugins.TempusDominus', true)
@section('plugins.Summernote', true)
@section('plugins.Magicsuggest', true)

<style type="text/css">
  .note-editable { font-size: 14px;}  
</style>


@section('content_header')
@stop

@section('content')
<div class="content-body">
    <div class="row">
      <div class="col-9">
        <form action="#" id="form_header" method="POST">
    <input type="hidden" name="subscriber_id" id="subscriber_id" class="form-control" value="{{ $subscriber_id }}">
    <!-- Variables para los items -->
    <input type="hidden" name="hdd_item_index" id="hdd_item_index" class="form-control" value="">
    <input type="hidden" name="hdd_item_id" id="hdd_item_id" class="form-control" value="">
    <input type="hidden" name="hdd_item_text" id="hdd_item_text" class="form-control" value="">
    <input type="hidden" name="hdd_item_type" id="hdd_item_type" class="form-control" value="">
    <input type="hidden" name="hdd_item_code" id="hdd_item_code" class="form-control" value="">
    <!-- Variables para descuentos fijos del cliente-->
    <input type="hidden" name="hdd_discount" id="hdd_discount" class="form-control" value="{{ ($sale->id && $sale->customer->discount)?$sale->customer->discount:0 }}">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
              <i class="far fa-user"></i> Datos generales</h3>
            </div>
            <div class="card-body">
                <div class="row">                  
                  <div class="col-12" style="font-size: 10pt">
                    <div class="icheck-primary d-inline">
                        {!! Form::checkbox('free', null, ($sale->prospect)?true:false, ['id'=>'free']) !!}
                        <label for="free">Prospecto <small>(No dirigido a clientes)</small></label>
                    </div>
                  </div>
                  <div class="col-6 mt-1" id="div_customer">
                    {{ Form::select('customer', $customers, ($sale->id)?$sale->customer_id:null, ['id'=>'customer', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                  </div>                  
                  <div class="col-6 mt-1" id="div_prospect" style="display: none;">
                    {!! Form::text('prospect', ($sale->prospect)?$sale->prospect:null, ['id'=>'prospect', 'class'=>'form-control', 'placeholder'=>'Dirigido a', 'title'=>'Contacto', 'required']) !!}
                  </div>
                  <div class="col-6 mt-1">
                    {!! Form::text('contact', ($sale->id)?$sale->contact:null, ['id'=>'contact', 'class'=>'form-control', 'placeholder'=>'Contacto', 'title'=>'Contacto', 'required']) !!}
                  </div>
                </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-3 input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><small>Cant</small></span>
                  </div>
                  {!! Form::number('quantity', 1, ['id'=>'quantity', 'class'=>'form-control', 'placeholder'=>'', 'min' => '1', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-9">
                  {{ Form::select('product', [], null, ['id'=>'product', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="col-3 input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><small>Precio</small>&nbsp;{{ session('coin') }}</span>
                  </div>
                  {!! Form::number('unit_price', null, ['id'=>'unit_price', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150', 'readonly']) !!}
                </div>
                <div class="col-3 input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><small>Desct</small></span></span>
                  </div>
                  {!! Form::number('discount', ($sale->id && $sale->customer->discount)?$sale->customer->discount:0, ['id'=>'discount', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="col-3 input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><small>Impuestos %</small></span></span>
                  </div>
                  {!! Form::number('tax', $tax, ['id'=>'tax', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="col-3" id="div_add">
                  <button type="button" id="btn_add_product" class="btn btn-primary btn-block"> Agregar</button>
                </div>                
                <div class="col-3" id="div_update" style="display: none">
                  <button type="button" id="btn_update_product" class="btn btn-primary btn-block"> Actualizar</button>
                </div>
                <div class="col-12">
                  <b>Existencia:</b> <span id="stock"></span>
                </div>
              </div>
            </div>
          </div>
        </form>

        <!-- detalle de productos -->
        <div class="card">
          <div class="card-header">
              <i class="fas fa-box-open"></i> Productos o servicios</h3>
          </div>
          <div class="card-body">
            <span id="items_sale"></span>
          </div>
        </div>
        <!-- detalle de productos -->
        
        <!-- observaciones y condiciones -->
        <div class="card">
          <div class="card-body">
            <form action="#" id="form_footer" method="POST">
              <div class="row">            
                <div class="form-group col-12">
                  <small><b>Este texto aparecerá arriba de los precios.</b> Max. 1500 caracteres.</small><span class="counter_observations text-muted float-right" style="font-size: 12px"></span>
                  {!! Form::textarea('observations', ($sale->id)?$sale->observations:null, ['id'=>'observations', 'class'=>'form-control', 'rows'=>'3', 'placeholder'=>'Escribe aqui alguna observación....', 'maxlength'=>'1500']) !!}
                </div>
                <div class="form-group col-12">
                  <small><b>Términos y condiciones</b> (puedes agregar tu datos bancarios para pago). Max. 1500 caracteres.</small>
                  <span class="counter_conditions small text-muted float-right" style="font-size: 12px"></span>
                  {!! Form::textarea('conditions', ($sale->id)?$sale->conditions:$setting->conditions, ['id'=>'conditions', 'class'=>'form-control', 'rows'=>'3', 'placeholder'=>'', 'maxlength'=>'1500']) !!}
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- observaciones y condiciones -->

      </div>

      <!-- RESUMEN -->
      <div class="col-3">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
            <i class="far fa-file"></i> RESUMEN {{ ($type=='C')?'COTIZACION':'FACTURA' }}</h3>
          </div>
          <div class="card-body">            
            <form action="#" id="form_summary" method="POST">
            <div class="row">
                <div class="col-12 mb-1" style="display:none">                    
                      <div class="icheck-primary d-inline">
                          {!! Form::checkbox('custom_folio', null, ($sale->custom_budget_folio || $sale->custom_sale_folio)?true:false, ['id'=>'custom_folio', 'disabled' => ($sale->id)?true:false]) !!}
                          <label for="custom_folio"><small>FOLIO PERSONAL</small></label>
                      </div>
                    <input type="text" name="folio" id="folio" class="form-control" value="{{ ($type=='C')?$sale->budget_folio:$sale->sale_folio }}" required="required" maxlength="10" style="display: {{ ($sale->custom_budget_folio || $sale->custom_sale_folio)?'solid':'none' }};margin-top: 2mm">
                </div>
            </div>
            <h4 class="text-center">
              <i class="far fa-calendar" aria-hidden="true"></i> {{ $today->format('d/m/Y') }}
            </h4>
            <span>Sub Total</span>
            <h3 class="font-bold">
                <b><div>{{ session('coin') }}<span id="sub_total">0,00</span></div></b>
            </h3>
            <span>Impuestos</span>
            <h3 class="font-bold">
                <div>{{ session('coin') }}<span id="total_tax">0,00</span></div>
            </h3>
            <span>Total</span>
            <h2 class="font-bold">
                <b><div>{{ session('coin') }}<span id="total">0,00</span></div></b>
            </h2>
              @if($type=='C')
              <div class="form-group">
                  <label>Vigencia *</label>
                    <div class="input-group date" id="calendar_due_date" data-target-input="nearest">
                        <input type="text" value="{{ $due_date->format('d/m/Y') }}" id="due_date" class="form-control datetimepicker-input" data-target="#calendar_due_date"/>
                        <div class="input-group-append" data-target="#calendar_due_date" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
              </div>
              @endif
              @if($type=='F')
                <div class="form-group">  
                  {{ Form::select('way_pay', ['1'=>'Efectivo', '2'=>'Cheque', '3'=>'Tarjeta', '4'=>'Transferencia'], $sale->way_pay, ['id'=>'way_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group">  
                  {{ Form::select('method_pay', ['1'=>'Pago total', '2'=>'Pagos parciales'], $sale->method_pay, ['id'=>'method_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group">  
                  {{ Form::select('condition_pay', ['1'=>'Contado', '2'=>'Crédito'], $sale->condition_pay, ['id'=>'condition_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
              @endif
            <hr/>
            <div class="m-t-sm">
                <button type="button" id="btn_modal_sale" class="btn btn-primary btn-block" disabled><b>{{ ($sale->id)?'Actualizar':'Generar' }} {{ ($type=='C')?'Cotización':'Factura' }}</b></button>
                <button type="button" id="btn_convert" class="btn btn-success btn-block" style="display: {{ ($sale->type=='C')?'solid':'none' }};"><b>Convertir a Factura</b></button>
                <button type="button" id="btn_reset" class="btn btn-info btn-block">Limpiar</button>
                <a href="{{ ($type=='C')?url('budgets'):url('sales') }}" class="btn grey btn-outline-secondary btn-block" title=""><i class="fa fa-hand-o-left" aria-hidden="true"></i> Salir</a>
            </div>
            </form>
          </div>
        </div>
      </div>
      <!-- RESUMEN COTIZACION -->

  </div>
</div>

<div class="modal fade" id="modal-options">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"> <strong>Opciones</strong></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">        
        <div class="form-group col-12">
          <div class="icheck-primary d-inline">
              {!! Form::checkbox('send_email', null, false, ['id'=>'send_email']) !!}
              <label for="send_email">Enviar por correo</label>
          </div>
        </div>        
        <div class="form-group col-12" id="div_to" style="display:none">
          <label>Correos *</label><small> Agregue correos que no esten en lista escribiendo y dando enter.</small>
          {!! Form::text('to[]', null, ['id'=>'to', 'class'=>'form-control', 'type'=>'text', 'required']) !!}
          <span id="msj_error_emails" style="color:#cc5965;font-weight:bold"></span>
        </div>
        <div class="form-group col-12">
          <div class="icheck-primary d-inline">
              {!! Form::checkbox('pdf_preview', null, false, ['id'=>'pdf_preview']) !!}
              <label for="pdf_preview">Descargar PDF al guardar</label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
          <button type="button" id="btn_close" class="btn grey btn-outline-secondary" data-dismiss="modal">Cerrar</button>        
          <button type="button" id="btn_submit" class="btn btn-outline-primary">Aceptar</button>
      </div>
    </div>  
  </div>
</div>

<!-- Modal nuevo cliente -->
<div class="modal fade text-left" id="modalCustomer" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="customer_modal"></div>
    </div>
  </div>
</div>
<!-- /Modal nuevo cliente -->

<div class="modal fade" id="modal-convert-sale">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="convert_modal"></div>
    </div>  
  </div>
</div>

<!-- Modal ask product/service -->
<div class="modal fade" id="modal-ask">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Registrar</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <button type="button" id="btn_new_product" class="btn btn-block btn-primary">Producto</button>
        <button type="button" id="btn_new_service" class="btn btn-block btn-primary">Servicio</button>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal ask product/service -->

<!-- Modal nuevo producto -->
<div class="modal fade" id="modal-product">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="product_modal"></div>
    </div>
  </div>
</div>
<!-- /Modal nuevo producto -->

<!-- Modal para nuevo servicio -->
<div class="modal fade" id="modal-service">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="service_modal"></div>
    </div>
  </div>
</div>
<!-- /Modal para nuevo servicio -->

@stop

@section('footer', 'Copyright © 2021. All rights reserved.' )

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
  
$('#send_email').on('click', function(event){
  (event.target.checked)?$('#div_to').show():$('#div_to').hide();
});

$("#btn_convert").on('click', function(event) {    
  var sale_id='{{ $sale->id }}';
  url = `{{URL::to("sales.load_convert_modal/")}}/${sale_id}`;
  $('#convert_modal').load(url);  
  $("#modal-convert-sale").modal("show");
});

$('#send_email').on('change', function(event){
  $('#to').val('');
  (event.target.checked)?$('#to').attr('disabled',false):$('#to').attr('disabled',true);
});

$('#quantity').blur(function() {
 ($(this).val()=='')?$(this).val(1):'';
});

$('#unit_price').blur(function() {
 ($(this).val()=='')?$(this).val(0):'';
});

$('#custom_folio').on('change', function(event){
  if(event.target.checked){
    $('#folio').show();
    $('#folio').focus();
  }else{
    $('#folio').hide();    
  }
});

$('#free').on('change', function(event){
  if(event.target.checked){
    $('#div_prospect').show();
    $('#div_customer').hide();
  }else{
    $('#div_prospect').hide();
    $('#div_customer').show();
  }
  $('#prospect').val('');
  $('#contact').val('');
  $('#to').val('');
  $('#customer').val(null).trigger('change');
});

function showModalCustomer(customer_id){
  url = `{{URL::to("customers.load")}}/${customer_id}`;
  $('#customer_modal').load(url);  
  $("#modalCustomer").modal("show");
}

function customer_CRUD(id){
    var validator = $("#form_customer").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit_customer').attr('disabled', true);
        var form_data = new FormData($("#form_documents")[0]);
        array_names.forEach((item) => form_data.append("names[]", item));
        array_occupations.forEach((item) => form_data.append("occupations[]", item));
        array_positions.forEach((item) => form_data.append("positions[]", item));
        array_phones.forEach((item) => form_data.append("phones[]", item));
        array_emails.forEach((item) => form_data.append("emails[]", item));
        array_mains.forEach((item) => form_data.append("mains[]", item));
        form_data.append("sucursal", ($('#sucursal').is(':checked'))?1:0);
        form_data.append("change_parent", ($('#change_parent').is(':checked'))?1:0);
        form_data.append("create_user", ($('#create_user').is(':checked'))?1:0);
        form_data.append("change_password", ($('#change_password').is(':checked'))?1:0);
        form_data.append("parent", $('#parent').val());
        form_data.append("name", $('#name').val());
        form_data.append("target", $('#target').val());
        form_data.append("phone", $('#phone').val());
        form_data.append("email", $('#email').val());
        form_data.append("street", $('#street').val());
        form_data.append("street_number", $('#street_number').val());
        form_data.append("neighborhood", $('#neighborhood').val());
        form_data.append("zipcode", $('#zipcode').val());
        form_data.append("city", $('#city').val());
        form_data.append("country", $('#country').val());
        form_data.append("state", $('#state').val());
        form_data.append("address", $('#address').val());
        form_data.append("notes", $('#notes').val());
        form_data.append("urls", $('#urls').val());
        form_data.append("bussines_name", $('#bussines_name').val());
        form_data.append("rfc", $('#rfc').val());
        form_data.append("bussines_address", $('#bussines_address').val());
        form_data.append("shipping_street", $('#shipping_street').val());
        form_data.append("shipping_address", $('#shipping_address').val());
        form_data.append("shipping_number", $('#shipping_number').val());
        form_data.append("shipping_neighborhood", $('#shipping_neighborhood').val());
        form_data.append("shipping_zipcode", $('#shipping_zipcode').val());
        form_data.append("shipping_country", $('#shipping_country').val());
        form_data.append("shipping_state", $('#shipping_state').val());
        form_data.append("shipping_city", $('#shipping_city').val());
        form_data.append("discount", $('#discount').val());
        $.ajax({
          url:(id==0)?'{{URL::to("customers")}}':'{{URL::to("customers")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit_customer').attr('disabled', false);
          $('#modalCustomer').modal('toggle');
          set_customer_emails(response.customer.id);
          refresh_select_customer(response.customer.subscriber_id, response.customer.id);
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_submit_customer').attr('disabled', false);
            var errorsHtml='';
            $.each(response.responseJSON.errors, function (key, value) {
              errorsHtml += '<li>' + value[0] + '</li>'; 
            });          
            toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 3000);
          }else{
            toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
          }
        });
    }
}

function refresh_select_customer(subscriber_id, customer_id){
  url = `{{URL::to('subscribers')}}/${subscriber_id}/customers`;                    
  $.get(url, function(response){
    $("#customer").empty();
    $("#customer").append(`<option value=0> **Nuevo Cliente** </option>`);
    response.data.forEach(element => {
      if(element.id==customer_id){
        $("#customer").append(`<option value=${element.id} selected> ${element.name} </option>`);
      }else{
        $("#customer").append(`<option value=${element.id}> ${element.name} </option>`);
      }
    });
  });
}

function product_CRUD(id){
        
    var validator = $("#form_product").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_product")[0]);
        form_data.append('spare', $('#chk_spare').is(":checked")?1:0);
        $.ajax({
          url:(id==0)?'{{URL::to("products")}}':'{{URL::to("products")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-product').modal('toggle');
          set_select_item(response.product.id,response.product.name);
          var item=response.product;
          $('#hdd_item_id').val(item.id);
          $('#hdd_item_text').val(item.name);
          $('#hdd_item_type').val('P');
          $('#hdd_item_code').val(item.code);
          $('#unit_price').val(item.price);
          $('#stock').html(item.stock);
          $("#quantity").attr('max', item.stock);
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_submit').attr('disabled', false);
            var errorsHtml='';
            $.each(response.responseJSON.errors, function (key, value) {
              errorsHtml += '<li>' + value[0] + '</li>'; 
            });          
            toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 3000);
          }else{
            toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
          }
        });
    }
}

function service_CRUD(id){
    var validator = $("#form_service").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_service")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("services")}}':'{{URL::to("services")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-service').modal('toggle');
          set_select_item(response.service.id,response.service.name);
          var item=response.service;
          $('#hdd_item_id').val(item.id);
          $('#hdd_item_text').val(item.name);
          $('#hdd_item_type').val('S');
          $('#hdd_item_code').val(item.code);
          $('#unit_price').val(item.price);
          $('#stock').html('NA');
          $("#quantity").removeAttr( "max");
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_submit').attr('disabled', false);
            var errorsHtml='';
            $.each(response.responseJSON.errors, function (key, value) {
              errorsHtml += '<li>' + value[0] + '</li>'; 
            });          
            toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 3000);
          }else{
            toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
          }
        });
    }
}

var ms_to=$('#to').magicSuggest({
  placeholder: "Seleccione los correos",
  required:true,
  maxSelection: 5,
  allowFreeEntries: true,
  allowDuplicates: false,
  //Todos los valores del select
  method:'get',
  data: [],
  //Valores iniciales del select
  value: [],
  valueField: 'email',
  displayField: 'email',
});

$("#customer").change( event => {
  if(event.target.value!=''){
    if(event.target.value==0){
      showModalCustomer(0);
    }else{
      set_customer_emails(event.target.value);
    }    
  }
});

function set_customer_emails(customer_id){
  url=`{{URL::to('customers/')}}/${customer_id}`;                    
  $.get(url, function(response){
    $('#contact').val(response.customer.main_contact.name);
    let discount=(response.customer.discount)?response.customer.discount:0;
    $('#hdd_discount').val(discount);
    $('#discount').val(discount);
    emails=response.customer.array_emails;
    if(emails.length>0){
      ms_to.clear();
      arr_emails=[];
      for (var i = 0; i < emails.length; i++) {
        arr_emails.push(emails[i]);
      }
      ms_to.setData(arr_emails); //se meten en lista todos
      ms_to.setValue([arr_emails[0]]); //se pone por default el 1ro
    }
  })
}

var array_ids={!! $array_ids !!};
var array_types={!! $array_types !!};
var array_codes={!! $array_codes !!};
var array_descriptions={!! $array_descriptions !!};
var array_quantities={!! $array_quantities !!};
var array_unit_prices={!! $array_unit_prices !!};
var array_sub_totals={!! $array_sub_totals !!};
var array_percent_discounts={!! $array_percent_discounts !!};
var array_discounts={!! $array_discounts !!};
var array_percent_taxes={!! $array_percent_taxes !!};
var array_taxes={!! $array_taxes !!};
var array_totals={!! $array_totals !!};
var max_items=20;

$("#btn_cancel").on('click', function(event) {
  clear_form_item();
  $('#btn_modal_sale').attr('disabled',false);
  $('#btn_reset').attr('disabled',false);
  $('#div_add').show();
  $('#div_update').hide();    
});

$("#btn_add_product").on('click', function(event) {
  add_item();
});

$("#btn_update_product").on('click', function(event) {
  update_item();
  $('#btn_modal_sale').attr('disabled',false);
  $('#btn_reset').attr('disabled',false);
  $('#div_add').show();
  $('#div_update').hide();
});

function edit_item(index) {
    id=array_ids[index];
    url=(array_types[index]=='P')?'{{URL::to("products")}}/'+id:'{{URL::to("services")}}/'+id;
    $.ajax({
      url: url,
      type: 'GET',
      data: {
        _token: "{{ csrf_token() }}", 
        id:id,
      },
    })
    .done(function(response) {        
      $('#div_add').hide();
      $('#div_update').show();
      $('#btn_modal_sale').attr('disabled',true);
      $('#btn_reset').attr('disabled',true);
      set_select_item(array_ids[index], array_descriptions[index]);
      $('#quantity').val(array_quantities[index]);
      $('#unit_price').val(array_unit_prices[index]);
      $('#discount').val(array_percent_discounts[index]);
      $('#tax').val(array_percent_taxes[index]);
      $('#hdd_item_id').val(array_ids[index]);
      $('#hdd_item_text').val(array_descriptions[index]);
      $('#hdd_item_type').val(array_types[index]);
      $('#hdd_item_code').val(array_codes[index]);
      $('#hdd_item_index').val(index);
      if(array_types[index]=='P'){
        $('#stock').html(response.data.stock);
        $("#quantity").prop('max', response.data.stock);
      }else{
        $('#stock').html('NA');
      }
    })
    .fail(function() {
      toastr_msg('error', '{{ config('app.name') }}', 'Ocurrió un error editando el producto', 2000);
    });
};

function update_item(){
  var validator = $("#form_header").validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    quantity=$('#quantity').val();
    unit_price=$('#unit_price').val();
    sub_total=quantity*unit_price;
    percent_discount=parseFloat($('#discount').val());
    discount=sub_total*(percent_discount/100);
    percent_tax=$('#tax').val();
    tax=(sub_total-discount)*(percent_tax/100);
    total=sub_total-discount+tax;
    var index=$('#hdd_item_index').val();
    array_ids[index]=$('#hdd_item_id').val();
    array_descriptions[index]=$('#hdd_item_text').val();
    array_types[index]=$('#hdd_item_type').val();
    array_codes[index]=$('#hdd_item_code').val();
    array_quantities[index]=quantity;
    array_unit_prices[index]=unit_price;
    array_sub_totals[index]=sub_total;
    array_percent_discounts[index]=percent_discount;
    array_discounts[index]=discount;
    array_percent_taxes[index]=percent_tax;
    array_taxes[index]=tax;
    array_totals[index]=total;
    clear_form_item();
    load_items();
    $('#btn_add_product').attr('disabled', (array_descriptions.length<max_items)?false:true);
    $('#btn_modal_sale').attr('disabled', (array_descriptions.length>0)?false:true);
  }
}

function add_item(){
  var validator = $("#form_header").validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    quantity=$('#quantity').val();
    unit_price=$('#unit_price').val();
    sub_total=quantity*unit_price;
    percent_discount=parseFloat($('#discount').val());
    discount=sub_total*(percent_discount/100);
    percent_tax=$('#tax').val();
    tax=(sub_total-discount)*(percent_tax/100);
    total=sub_total-discount+tax;
    array_ids.push($('#hdd_item_id').val());
    array_descriptions.push($('#hdd_item_text').val());
    array_types.push($('#hdd_item_type').val());
    array_codes.push($('#hdd_item_code').val());
    array_quantities.push(quantity);
    array_unit_prices.push(unit_price);
    array_sub_totals.push(sub_total);
    array_percent_discounts.push(percent_discount);
    array_discounts.push(discount);
    array_percent_taxes.push(percent_tax);
    array_taxes.push(tax);
    array_totals.push(total);
    clear_form_item();
    load_items();
    $('#btn_add_product').attr('disabled', (array_descriptions.length<max_items)?false:true);
    $('#btn_modal_sale').attr('disabled', (array_descriptions.length>0)?false:true);
  }    
}

function remove_item(index) {
  array_ids.splice(index, 1);
  array_types.splice(index, 1);
  array_codes.splice(index, 1);
  array_descriptions.splice(index, 1);
  array_quantities.splice(index, 1);
  array_unit_prices.splice(index, 1);
  array_sub_totals.splice(index, 1);
  array_percent_discounts.splice(index, 1);
  array_discounts.splice(index, 1);
  array_percent_taxes.splice(index, 1);
  array_taxes.splice(index, 1);
  array_totals.splice(index, 1);
  load_items();
  $('#btn_add_product').attr('disabled', (array_descriptions.length<max_items)?false:true);
  $('#btn_modal_sale').attr('disabled', (array_descriptions.length>0)?false:true);
};

function load_items(){
  $.ajax({
    url: `{{URL::to("sales.load_items")}}`,
    type: 'POST',
    data: {
      _token: "{{ csrf_token() }}", 
      array_codes:array_codes,
      array_descriptions:array_descriptions,
      array_quantities:array_quantities,
      array_unit_prices:array_unit_prices,
      array_sub_totals:array_sub_totals,
      array_percent_discounts:array_percent_discounts,
      array_discounts:array_discounts,
      array_percent_taxes:array_percent_taxes,
      array_taxes:array_taxes,
      array_totals:array_totals
    },
  })
  .done(function(response) {
    (response=='')?clear_summary():'';
    $('#items_sale').html(response);
  })
  .fail(function() {
    //
  });
}

function set_select_item(id, text){
  $('#product')
    .append('<option selected value="'+id+'" data-select2-type="S">'+text+'</option>');
  $('#product').trigger('change');
}


$("#btn_reset").on('click', function(event) {
  //
});

function clear_summary(){
  $('#sub_total').html('0,00');
  $('#total_discount').html('0,00');
  $('#total_discount').html('0,00');
  $('#total_tax').html('0,00');
  $('#total').html('0,00');
}

function clear_form_item(){
  $('#product').empty();
  $('#quantity').val(1);
  $('#unit_price').val(0);
  $('#discount').val($('#hdd_discount').val());
  $('#tax').val({{ $tax }});
  $('#stock').html('');
}

function reset_form(){
  $('#customer').val(null).trigger('change');
  $('#contact').val('');
  $('#free').prop('checked', false);
  $('#prospect').val('');
  $('#send_email').prop('checked', false);
  $('#to').val('');
  $('#created_by').val('');
  $('#observations').val('');
  $('#conditions').val('');
  $('#items_sale').html('');  
  $('#btn_modal_sale').attr('disabled', true);
  $('#btn_print_sale').attr('disabled', true);
  $('#custom_folio').prop('checked', false);
  $('#folio').val('');
  $('#folio').hide();  
  array_descriptions=[];
  array_quantities=[];
  array_unit_prices=[];
  array_sub_totals=[];
  array_percent_discounts=[];
  array_discounts=[];
  array_percent_taxes=[];
  array_taxes=[];
  array_totals=[];
  clear_form_item();
  clear_summary();
}

$('#btn_modal_sale').click(function(event) {
  var validator_summary = $("#form_summary" ).validate();
  var validator_footer = $("#form_footer" ).validate();
  formulario_validado = (validator_footer.form() && validator_summary.form());
  if(formulario_validado){
    $('#send_email').prop('checked', false);
    $('#pdf').prop('checked', false);
    $("#modal-options").modal("show");  
  }
});

$('#btn_submit').click(function(event) {
  send_email=($('#send_email').is(':checked'))?1:0;
  if(send_email && ms_to.getValue()==''){
    $('#msj_error_emails').html('Debe colocar al menos un correo');
  }else{
    var id={{ ($sale->id)?$sale->id:0 }};
    var type='{{ $type }}';
    $(this).attr('disabled', true);
    var pdf_preview=($('#pdf_preview').is(':checked'))?1:0;
    $.ajax({
      url:(id==0)?'{{URL::to("sales")}}':'{{URL::to("sales")}}/'+id,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}",
        _method:(id>0)?'PUT':'',
        type:type,
        subscriber_id:$('#subscriber_id').val(),
        custom_folio:($('#custom_folio').is(':checked'))?1:0,
        send_email:($('#send_email').is(':checked'))?1:0,
        free:($('#free').is(':checked'))?1:0,
        prospect:$('#prospect').val(),
        send_email:send_email,
        to:ms_to.getValue(),
        folio:$('#folio').val(),
        due_date:$('#due_date').val(),
        way_pay:$('#way_pay').val(),
        method_pay:$('#method_pay').val(),
        condition_pay:$('#condition_pay').val(),
        customer_id:$('#customer').val(),
        contact:$('#contact').val(),
        created_by:$('#created_by').val(),
        ids:array_ids,
        types:array_types,
        quantities:array_quantities,
        unit_prices:array_unit_prices,
        percent_discounts:array_percent_discounts,
        percent_taxes:array_percent_taxes,
        observations:$('#observations').val(),
        conditions:$('#conditions').val()
      },
    })
    .done(function(response) {
      $('#btn_submit').attr('disabled', false);
      $('#modal-options').modal('toggle');
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
      (id==0)?reset_form():'';        
      if(pdf_preview){
        setTimeout(() => {
          var my_url = `{{URL::to('sales.download_sale/')}}/${response.sale.id}`;
          window.open(my_url, '_self');
        }, 200);          
        //let pdfWindow = window.open("")
        //pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.base64) + "'></iframe>")
      }      
    })
    .fail(function(response) {
      $('#btn_modal_sale').attr('disabled', false);
      if(response.status == 422){
        $('#btn_modal_sale').attr('disabled', false);
        var errorsHtml='';
        $.each(response.responseJSON.errors, function (key, value) {
          errorsHtml += '<li>' + value[0] + '</li>'; 
        });          
        toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 3000);
      }else{
        toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
      }
    });
  }    
});


$("#product").select2({
  language: 'es',
  placeholder: "Producto o servicio",
  minimumInputLength: {{ $min_input_length }},
  //minimumInputLength: 2, //sino se setea despliega la 1ra vez normal
  minimumResultsForSearch: 10,
  ajax: {
    url: '{{URL::to("products_services")}}',
    //delay: 50,
    dataType: "json",
    type: "GET",
    data: function (params) {
      return {
        q: params.term,
      };
    },
    processResults: function (data) {
      return {
          results: $.map(data, function (item) {
              return {
                  id: item.id,
                  text: item.name,
                  type: item.type,
                  code: item.code,
                  price: item.price,
                  stock: item.stock,
              }
          })
      };
    }
  }
});

$('#product').on('select2:select', function (e) {
    var item = e.params.data;
    if(item.id==0){
      showModalAsk();
    }else{
      $('#hdd_item_id').val(item.id);
      $('#hdd_item_text').val(item.text);
      $('#hdd_item_type').val(item.type);
      $('#hdd_item_code').val(item.code);
      $('#unit_price').val(item.price);
      $('#stock').html(item.stock);
      (item.type=='P')?$("#quantity").attr('max', item.stock):$("#quantity").removeAttr( "max");
    }
});

$("#btn_new_product").on('click', function(event) {    
  $("#modal-ask").modal("toggle");
  showModalProduct(0);
});

$("#btn_new_service").on('click', function(event) {    
  $("#modal-ask").modal("toggle");
  showModalService(0);
});

function showModalProduct(id){
  url = '{{URL::to("products.load")}}/'+id;
  $('#product_modal').load(url);  
  $("#modal-product").modal("show");
}

function showModalService(id){
  url = '{{URL::to("services.load")}}/'+id;
  $('#service_modal').load(url);  
  $("#modal-service").modal("show");
}

function showModalAsk(){
  $("#modal-ask").modal("show");
}


$(document).ready(function(){
                        
  if('{{ $sale->id }}'!=''){
    $('#customer').val('{{ $sale->customer_id }}').trigger('change');
  }

  $("#customer").select2({
      language: "es",
      placeholder: "Cliente",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
  });

  $("#way_pay").select2({
      language: "es",
      placeholder: "Forma de pago",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
  });

  $("#method_pay").select2({
      language: "es",
      placeholder: "Método de pago",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
  });

  $("#condition_pay").select2({
      language: "es",
      placeholder: "Condiciones de pago",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
  });
  
  if('{{ $sale->id }}'!='' && '{{ $sale->prospect }}'!=''){
    $('#div_prospect').show();
    $('#div_customer').hide();    
  }  

  load_items();
  
  var dueDate;
  dueDate=new Date();
  dueDate.setHours(0,0,0,0);
    
  //Date range picker
  $('#calendar_due_date').datetimepicker({
      locale:'es',
      format: 'DD/MM/YYYY',
      minDate: dueDate
  });

  $('#btn_modal_sale').attr('disabled', (array_descriptions.length>0)?false:true);    

  $('#observations').summernote({
    lang: "es-ES",
    placeholder: 'Escribe aqui observaciones ...',
    height: 150,
    lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '2.0', '3.0'],    
    toolbar: [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['fontsize', ['fontsize']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']]
      ],
      callbacks: {
        onKeydown: function (e) {
            var t = e.currentTarget.innerText;
            if (t.trim().length >= 1500) {
                if (e.keyCode != 8)
                    e.preventDefault();
            }
        },
        onKeyup: function (e) {
            var t = e.currentTarget.innerText;
            $('.counter_observations').text(1500 - t.trim().length);
        },
        onPaste: function (e) {
            var t = e.currentTarget.innerText;
            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
            e.preventDefault();
            var all = t + bufferText;
            if(all.length > 1500) {       
                document.execCommand('insertText', false, bufferText.trim().substring(0, 1500 - t.length));
                $('.counter_observations').text(all.trim().substring(0, 1500));
            }else {
                document.execCommand('insertText', false, bufferText);
                $('.counter_observations').text(all);
            }
        }
      }
  });

  $('#conditions').summernote({
    lang: "es-ES",
    placeholder: 'Escribe aqui términos y condiciones ...',
    height: 150,
    lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '2.0', '3.0'],    
    toolbar: [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['fontsize', ['fontsize']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']]
      ],
      callbacks: {
        onKeydown: function (e) {
            var t = e.currentTarget.innerText;
            if (t.trim().length >= 1500) {
                if (e.keyCode != 8)
                    e.preventDefault();
            }
        },
        onKeyup: function (e) {
            var t = e.currentTarget.innerText;
            $('.counter_conditions').text(1500 - t.trim().length);
        },
        onPaste: function (e) {
            var t = e.currentTarget.innerText;
            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
            e.preventDefault();
            var all = t + bufferText;
            if(all.length > 1500) {       
                document.execCommand('insertText', false, bufferText.trim().substring(0, 1500 - t.length));
                $('.counter_conditions').text(all.trim().substring(0, 1500));
            }else {
                document.execCommand('insertText', false, bufferText);
                $('.counter_conditions').text(all);
            }
        }
      }
  });


});
</script>
@stop