@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.ICheck', true)
@section('plugins.Moment', true)
@section('plugins.TempusDominus', true)
@section('plugins.Summernote', true)

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
          <input type="hidden" name="hdd_item_index" id="hdd_item_index" class="form-control" value="">
          <input type="hidden" name="hdd_item_type" id="hdd_item_type" class="form-control" value="">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
              <i class="far fa-user"></i> Datos generales</h3>
            </div>
            <div class="card-body">
                <div class="row">                  
                  <div class="col-4" style="font-size: 10pt;display: none;">
                    <div class="icheck-primary d-inline">
                        {!! Form::checkbox('free', null, ($purchase->prospect)?true:false, ['id'=>'free']) !!}
                        <label for="free">Prospecto <small>(No dirigida a proveedores)</small></label>
                    </div>
                  </div>
                  <div class="col-4" style="font-size: 10pt">
                    <div class="icheck-primary d-inline">
                        {!! Form::checkbox('send_email', null, false, ['id'=>'send_email']) !!}
                        <label for="send_email">Enviar por correo</label>
                    </div>
                  </div>
                  <div class="col-4">
                    {!! Form::email('to', null, ['id'=>'to', 'class'=>'form-control', 'placeholder'=>'Correo a enviar', 'title'=>'', 'required', 'disabled']) !!}
                  </div>
                  <div class="col-4">
                  </div>
                  <div class="col-4 mt-1" id="div_supplier">
                    {{ Form::select('supplier', $suppliers, ($purchase->id)?$purchase->supplier_id:null, ['id'=>'supplier', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                  </div>                  
                  <div class="col-4 mt-1" id="div_prospect" style="display: none;">
                    {!! Form::text('prospect', ($purchase->prospect)?$purchase->prospect:null, ['id'=>'prospect', 'class'=>'form-control', 'placeholder'=>'Dirigido a', 'title'=>'Contacto', 'required']) !!}
                  </div>
                  <div class="col-4 mt-1">
                    {!! Form::text('contact', ($purchase->id)?$purchase->contact:null, ['id'=>'contact', 'class'=>'form-control', 'placeholder'=>'Contacto', 'title'=>'Contacto', 'required']) !!}
                  </div>
                  <div class="col-4 mt-1">
                    {!! Form::text('created_by', ($purchase->id)?$purchase->created_by:Auth::user()->name, ['id'=>'created_by', 'class'=>'form-control', 'placeholder'=>'Elaborada por...', 'title'=>'Elaborada por', 'required']) !!}
                  </div>
                </div>
            </div>
          </div>

          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="form-group col-12">
                  {{ Form::select('product', [], null, ['id'=>'product', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="col-3 input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><small>Cantidad</small></span>
                  </div>
                  {!! Form::number('quantity', 1, ['id'=>'quantity', 'class'=>'form-control', 'placeholder'=>'', 'min' => '1', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="col-3 input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><small>Costo</small>&nbsp;{{ session('coin') }}</span>
                  </div>
                  {!! Form::number('unit_price', 0, ['id'=>'unit_price', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="col-3 input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><small>Impuesto %</small></span></span>
                  </div>
                  {!! Form::number('tax', $tax, ['id'=>'tax', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="col-3" id="div_add">
                  <button type="button" id="btn_add_product" class="btn btn-primary btn-block"> Agregar</button>
                </div>
                <div class="col-3" id="div_update" style="display: none">
                  <button type="button" id="btn_update_product" class="btn btn-primary btn-block"> Actualizar</button>
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
            <span id="items_purchase"></span>
          </div>
        </div>
        <!-- detalle de productos -->
        
        <!-- observaciones y condiciones -->
        <div class="card">
          <div class="card-body">
            <form action="#" id="form_footer" method="POST">
              <div class="row">            
                <div class="form-group col-12">
                  <small><b>Observaciones</b> Max. 1500 caracteres.</small><span class="counter_observations text-muted float-right" style="font-size: 12px"></span>
                  {!! Form::textarea('observations', ($purchase->id)?$purchase->observations:null, ['id'=>'observations', 'class'=>'form-control', 'rows'=>'3', 'placeholder'=>'Escribe aqui alguna observación....', 'maxlength'=>'1500']) !!}
                </div>
                <div class="form-group col-12">
                  <small><b>Términos y condiciones</b> Max. 1500 caracteres.</small>
                  <span class="counter_conditions small text-muted float-right" style="font-size: 12px"></span>
                  {!! Form::textarea('conditions', ($purchase->id)?$purchase->conditions:$setting->conditions, ['id'=>'conditions', 'class'=>'form-control', 'rows'=>'3', 'placeholder'=>'', 'maxlength'=>'1500']) !!}
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
            <i class="far fa-file"></i> RESUMEN {{ ($type=='O')?'ORDEN':'COMPRA' }}</h3>
          </div>
          <div class="card-body">            
            <form action="#" id="form_summary" method="POST">
            <div class="row">
                <div class="col-12 mb-1" style="display:none">                    
                      <div class="icheck-primary d-inline">
                          {!! Form::checkbox('custom_folio', null, ($purchase->custom_order_folio || $purchase->custom_purchase_folio)?true:false, ['id'=>'custom_folio', 'disabled' => ($purchase->id)?true:false]) !!}
                          <label for="custom_folio"><small>FOLIO PERSONAL</small></label>
                      </div>
                    <input type="text" name="folio" id="folio" class="form-control" value="{{ ($type=='O')?$purchase->order_folio:$purchase->purchase_folio }}" required="required" maxlength="10" style="display: {{ ($purchase->custom_order_folio || $purchase->custom_purchase_folio)?'solid':'none' }};margin-top: 2mm">
                </div>
            </div>
            <h4 class="text-center">
              <i class="far fa-calendar" aria-hidden="true"></i> {{ $today->format('d/m/Y') }}
            </h4>
            <span>Sub Total</span>
            <h3 class="font-bold">
                <b><div>{{ session('coin') }} <span id="sub_total">0,00</span></div></b>
            </h3>
            <span>Impuesto</span>
            <h3 class="font-bold">
                <div>{{ session('coin') }} <span id="total_tax">0,00</span></div>
            </h3>
            <span>Total</span>
            <h2 class="font-bold">
                <b><div>{{ session('coin') }} <span id="total">0,00</span></div></b>
            </h2>
              @if($type=='O')
              <div class="form-group">
                  <label>Vencimiento *</label>
                    <div class="input-group date" id="calendar_due_date" data-target-input="nearest">
                        <input type="text" id="due_date" class="form-control datetimepicker-input" data-target="#calendar_due_date"/>
                        <div class="input-group-append" data-target="#calendar_due_date" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
              </div>
              @endif
            <hr/>
            <div class="m-t-sm">
                <button type="button" id="btn_submit_purchase" class="btn btn-primary btn-block" disabled><b>{{ ($purchase->id)?'Actualizar':'Generar' }} {{ ($type=='O')?'Orden':'Compra' }}</b></button>
                <button type="button" id="btn_convert" class="btn btn-success btn-block" style="display: {{ ($purchase->type=='O')?'solid':'none' }};"><b>Convertir a Compra</b></button>
                <button type="button" id="btn_reset" class="btn btn-info btn-block">Limpiar</button>
                <a href="{{ ($type=='O')?url('orders'):url('purchases') }}" class="btn grey btn-outline-secondary btn-block" title=""><i class="fa fa-hand-o-left" aria-hidden="true"></i> Salir</a>
            </div>
            </form>
          </div>
        </div>
      </div>
      <!-- RESUMEN ORDEN -->

  </div>
</div>

<!-- Modal Nuevo Proveedor -->
<div class="modal fade text-left" id="modalSupplier" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="supplier_modal"></div>
    </div>
  </div>
</div>
<!-- /Modal Nuevo Proveedor -->

<div class="modal fade" id="modal-convert-purchase">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="convert_modal"></div>
    </div>  
  </div>
</div>

@stop

@section('footer', 'Copyright © 2021. All rights reserved.' )

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
  
$("#btn_convert").on('click', function(event) {    
  var purchase_id='{{ $purchase->id }}';
  url = `{{URL::to("purchases.load_convert_modal/")}}/${purchase_id}`;
  $('#convert_modal').load(url);  
  $("#modal-convert-purchase").modal("show");
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
    $('#div_supplier').hide();
  }else{
    $('#div_prospect').hide();
    $('#div_supplier').show();
  }
  $('#prospect').val('');
  $('#contact').val('');
  $('#to').val('');
  $('#supplier').val(null).trigger('change');
});

function showModalSupplier(supplier_id){
  url = `{{URL::to("suppliers.load")}}/${supplier_id}`;
  $('#supplier_modal').load(url);  
  $("#modalSupplier").modal("show");
}

function supplier_CRUD(id){
        
    var validator = $("#form_supplier").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit_supplier').attr('disabled', true);
        var form_data = new FormData($("#form_documents")[0]);
        array_names.forEach((item) => form_data.append("names[]", item));
        array_occupations.forEach((item) => form_data.append("occupations[]", item));
        array_positions.forEach((item) => form_data.append("positions[]", item));
        array_phones.forEach((item) => form_data.append("phones[]", item));
        array_emails.forEach((item) => form_data.append("emails[]", item));
        array_mains.forEach((item) => form_data.append("mains[]", item));
        form_data.append("name", $('#name').val());
        form_data.append("target", $('#target').val());
        form_data.append("phone", $('#phone').val());
        form_data.append("email", $('#email').val());
        form_data.append("country", $('#country').val());
        form_data.append("state", $('#state').val());
        form_data.append("address", $('#address').val());
        form_data.append("city", $('#city').val());
        form_data.append("zipcode", $('#zipcode').val());
        form_data.append("location", $('#location').val());
        form_data.append("urls", $('#urls').val());
        form_data.append("bank_accounts", $('#bank_accounts').val());
        form_data.append("bussines_name", $('#bussines_name').val());
        form_data.append("rfc", $('#rfc').val());
        form_data.append("bussines_address", $('#bussines_address').val());
        form_data.append("notes", $('#notes').val());
        $.ajax({
          url:(id==0)?'{{URL::to("suppliers")}}':'{{URL::to("suppliers")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit_supplier').attr('disabled', false);
          $('#modalSupplier').modal('toggle');
          $('#contact').val(response.supplier.main_contact.name);
          $('#to').val(response.supplier.email);
          refresh_select_supplier(response.supplier.subscriber_id, response.supplier.id);
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_submit_supplier').attr('disabled', false);
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

function refresh_select_supplier(subscriber_id, supplier_id){
  url = `{{URL::to('subscribers')}}/${subscriber_id}/suppliers`;                    
  $.get(url, function(response){
    $("#supplier").empty();
    $("#supplier").append(`<option value=0> **Nuevo Proveedor** </option>`);
    response.data.forEach(element => {
      if(element.id==supplier_id){
        $("#supplier").append(`<option value=${element.id} selected> ${element.name} </option>`);
      }else{
        $("#supplier").append(`<option value=${element.id}> ${element.name} </option>`);
      }
    });
  });
}

$("#supplier").change( event => {
  if(event.target.value!=''){
    if(event.target.value==0){
      showModalSupplier(0);
    }else{
      url=`{{URL::to('suppliers/')}}/${event.target.value}`;                    
      $.get(url, function(response){
        $('#contact').val(response.supplier.main_contact.name);
        $('#to').val(response.supplier.email);
      })    
    }    
  }
});

var array_ids={!! $array_ids !!};
var array_types={!! $array_types !!};
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
  clear_form_product();
  $('#btn_submit_purchase').attr('disabled',false);
  $('#btn_reset').attr('disabled',false);
  $('#div_add').show();
  $('#div_update').hide();    
});

$("#btn_add_product").on('click', function(event) {
  add_product();
});

$("#btn_update_product").on('click', function(event) {
  update_product();
  $('#btn_submit_purchase').attr('disabled',false);
  $('#btn_reset').attr('disabled',false);
  $('#div_add').show();
  $('#div_update').hide();
});

function edit_item(index) {
  $('#div_add').hide();
  $('#div_update').show();
  $('#btn_submit_purchase').attr('disabled',true);
  $('#btn_reset').attr('disabled',true);
  set_select_item(array_ids[index], array_descriptions[index]);
  //$('#description').val(array_descriptions[index]);
  $('#quantity').val(array_quantities[index]);
  $('#unit_price').val(array_unit_prices[index]);
  $('#tax').val(array_percent_taxes[index]);
  $('#hdd_item_type').val(array_types[index]);
  $('#hdd_item_index').val(index);
  //remove_item(index);
};

function update_product(){
  var validator = $("#form_header").validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    quantity=$('#quantity').val();
    unit_price=$('#unit_price').val();
    sub_total=quantity*unit_price;
    percent_discount=0;
    discount=sub_total*(percent_discount/100);
    percent_tax=$('#tax').val();
    tax=(sub_total-discount)*(percent_tax/100);
    total=sub_total-discount+tax;
    
    var index=$('#hdd_item_index').val();
    var product=$('#product').select2('data');
    array_ids[index]=product[0].id;
    array_types[index]=$('#hdd_item_type').val();
    array_descriptions[index]=product[0].text;
    array_quantities[index]=quantity;
    array_unit_prices[index]=unit_price;
    array_sub_totals[index]=sub_total;
    array_percent_discounts[index]=percent_discount;
    array_discounts[index]=discount;
    array_percent_taxes[index]=percent_tax;
    array_taxes[index]=tax;
    array_totals[index]=total;
    clear_form_product();
    load_items();
    $('#btn_add_product').attr('disabled', (array_descriptions.length<max_items)?false:true);
    $('#btn_submit_purchase').attr('disabled', (array_descriptions.length>0)?false:true);
  }
}

function add_product(){
  var validator = $("#form_header").validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    quantity=$('#quantity').val();
    unit_price=$('#unit_price').val();
    sub_total=quantity*unit_price;
    percent_discount=0;
    discount=sub_total*(percent_discount/100);
    percent_tax=$('#tax').val();
    tax=(sub_total-discount)*(percent_tax/100);
    total=sub_total-discount+tax;

    //array_descriptions.push($('#description').val());
    var product=$('#product').select2('data');
    console.log(product);
    array_ids.push(product[0].id);
    array_types.push(product[0].type);
    array_descriptions.push(product[0].text);
    array_quantities.push(quantity);
    array_unit_prices.push(unit_price);
    array_sub_totals.push(sub_total);
    array_percent_discounts.push(percent_discount);
    array_discounts.push(discount);
    array_percent_taxes.push(percent_tax);
    array_taxes.push(tax);
    array_totals.push(total);
    clear_form_product();
    load_items();
    $('#btn_add_product').attr('disabled', (array_descriptions.length<max_items)?false:true);
    $('#btn_submit_purchase').attr('disabled', (array_descriptions.length>0)?false:true);
  }    
}

function remove_item(index) {
  array_ids.splice(index, 1);
  array_types.splice(index, 1);
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
  $('#btn_submit_purchase').attr('disabled', (array_descriptions.length>0)?false:true);
};

function load_items(){
  $.ajax({
    url: `{{URL::to("purchases.load_items")}}`,
    type: 'POST',
    data: {
      _token: "{{ csrf_token() }}", 
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
    $('#items_purchase').html(response);
  })
  .fail(function() {
    //
  });
}

function set_select_item(id, text){
  $('#product')
    .empty()
    .append('<option selected value="'+id+'">'+text+'</option>');
  $('#product').select2('data', {
    id: id,
    text: text
  });
  $('#product').trigger('change');
}

$("#btn_reset").on('click', function(event) {
  //set_select_item(1,'Laptop');
  //reset_form();
});

function clear_summary(){
  $('#sub_total').html('0,00');
  $('#total_discount').html('0,00');
  $('#total_tax').html('0,00');
  $('#total').html('0,00');
}

function clear_form_product(){
  $('#product').empty();
  //$('#description').val('');
  $('#quantity').val(1);
  $('#unit_price').val(0);
  $('#tax').val({{ $tax }});
}

function reset_form(){
  $('#supplier').val(null).trigger('change');
  $('#contact').val('');
  $('#free').prop('checked', false);
  $('#prospect').val('');
  $('#send_email').prop('checked', false);
  $('#to').val('');
  $('#created_by').val('');
  $('#observations').val('');
  $('#conditions').val('');
  $('#items_purchase').html('');  
  $('#btn_submit_purchase').attr('disabled', true);
  $('#btn_print_purchase').attr('disabled', true);
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
  clear_form_product();
  clear_summary();
}

$('#btn_submit_purchase').click(function(event) {
    var validator_summary = $("#form_summary" ).validate();
    var validator_footer = $("#form_footer" ).validate();
    formulario_validado = (validator_footer.form() && validator_summary.form());
    if(formulario_validado){
      var id={{ ($purchase->id)?$purchase->id:0 }};
      var type='{{ $type }}';
      $(this).attr('disabled', true);
      $.ajax({
        url:(id==0)?'{{URL::to("purchases")}}':'{{URL::to("purchases")}}/'+id,
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
          to:$('#to').val(),
          folio:$('#folio').val(),
          due_date:$('#due_date').val(),
          supplier_id:$('#supplier').val(),
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
        toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        if(id==0){
          reset_form();
          setTimeout(() => {
            var my_url = `{{URL::to('purchases.download_purchase/')}}/${response.purchase.id}`;
            window.open(my_url, '_self');
          }, 200);          
          //let pdfWindow = window.open("")
          //pdfWindow.document.write("<iframe width='100%' height='100%' src='data:application/pdf;base64, " + encodeURI(response.base64) + "'></iframe>")
        }
      })
      .fail(function(response) {
        $('#btn_submit_purchase').attr('disabled', false);
        if(response.status == 422){
          $('#btn_submit_purchase').attr('disabled', false);
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
                  type: item.type
              }
          })
      };
    }
  }
});

$('#product').on('select2:select', function (e) {
    var data = e.params.data;
    console.log(data);
});

$(document).ready(function(){
                      
  $("#supplier").select2({
      language: "es",
      placeholder: "Proveedor",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
  });

  
  if('{{ $purchase->id }}'!='' && '{{ $purchase->prospect }}'!=''){
    $('#div_prospect').show();
    $('#div_supplier').hide();    
  }  

  load_items();
  
  var dueDate;

  if({{ ($purchase->id)?1:0 }}){
    str_date='{{ ($purchase->due_date)?$purchase->due_date->format('d-m-Y'):'' }}';
    dueDate=new Date(str_date.replace( /(\d{2})-(\d{2})-(\d{4})/, "$2/$1/$3"));
  }else{
    dueDate=new Date();
    dueDate.setDate(dueDate.getDate(new Date())+7);
  }
    
  //Date range picker
  $('#calendar_due_date').datetimepicker({
      locale:'es',
      format: 'DD/MM/YYYY',
      minDate: dueDate
  });

  $('#btn_submit_purchase').attr('disabled', (array_descriptions.length>0)?false:true);    



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