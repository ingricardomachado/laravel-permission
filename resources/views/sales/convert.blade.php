<form action="" method="POST" id="form_convert">
  <div class="modal-header">
    <h4 class="modal-title"><i class="fas fa-shopping-cart"></i> <strong>Convertir en Factura</strong></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>      
  <div class="modal-body">
      <div class="form-group col-sm-12">
        <b>Fecha de Factura</b> {{ Carbon\Carbon::now()->format('d/m/Y') }}<br>
        <b>Cliente</b> {{ $sale->customer->name }}<br>
        <b>Cotización</b> {{ $sale->folio }}<br>
        <b>Monto</b> {{ money_fmt($sale->total) }}
      </div>
      <div class="form-group">  
        {{ Form::select('way_pay', ['1'=>'Efectivo', '2'=>'Cheque', '3'=>'Tarjeta', '4'=>'Transferencia'], null, ['id'=>'way_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
      </div>
      <div class="form-group">  
        {{ Form::select('method_pay', ['1'=>'Pago total', '2'=>'Pagos parciales'], null, ['id'=>'method_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
      </div>
      <div class="form-group">  
        {{ Form::select('condition_pay', ['1'=>'Contado', '2'=>'Crédito'], null, ['id'=>'condition_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
      </div>
      <div class="form-group col-sm-12" style="display:none">
          <div class="icheck-primary d-inline">
              {!! Form::checkbox('convert_custom_folio', null, false, ['id'=>'convert_custom_folio']) !!}
              <label for="convert_custom_folio">Folio personal</label><br>
              <small> Si no marca la casilla el sistema generará un consecutivo automático.</small>
          </div>
          <div id="div_convert_folio" style="display: none;margin-top: 2mm;">
              {!! Form::text('convert_folio', null, ['id'=>'convert_folio', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Folio personal...', 'maxlength'=>'10', 'required']) !!}
          </div>
      </div>
      <div class="form-group col-sm-12">
          <div class="icheck-primary d-inline">
              {!! Form::checkbox('convert_send_email', null, false, ['id'=>'convert_send_email']) !!}
              <label for="convert_send_email">Enviar por correo</label><br>
              <small> Marca la casilla si quieres enviar la factura por correo al cliente.</small>
          </div>
          <div id="div_to" style="display:none;margin-top: 2mm;">
            {!! Form::email('convert_to', ($sale->customer_id)?$sale->customer->email:'', ['id'=>'convert_to', 'class'=>'form-control', 'placeholder'=>'Correo a enviar...', 'title'=>'', 'required']) !!}
          </div>
      </div>
  </div>
  <div class="modal-footer">
      <button type="button" id="btn_close" class="btn grey btn-outline-secondary" data-dismiss="modal">Cerrar</button>        
      <button type="button" id="btn_convert_sale" class="btn btn-outline-primary">Convertir</button>
  </div>
</form>
<script>

$('#convert_custom_folio').on('change', function(event) { 
  (event.target.checked)?$('#div_convert_folio').show():$('#div_convert_folio').hide();
});

$('#convert_send_email').on('change', function(event) { 
  (event.target.checked)?$('#div_to').show():$('#div_to').hide();
});

$("#btn_convert_sale").on('click', function(event) {    
  var validator = $("#form_convert").validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    $(this).attr('disabled', true);
    var id={{ $sale->id }};
    $.ajax({
        url: '{{URL::to("sales.convert")}}/'+id,
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          way_pay:$('#way_pay').val(),
          method_pay:$('#method_pay').val(),
          condition_pay:$('#condition_pay').val(),
          custom_folio:($('#convert_custom_folio').is(":checked"))?1:0,
          folio:$('#convert_folio').val(),
          send_email:($('#convert_send_email').is(":checked"))?1:0,
          to:$('#convert_to').val() 
        },
    })
    .done(function(response) {
      $('#btn_convert_sale').attr('disabled', false);
      $('#modal-convert-sale').modal('toggle');
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
      reset_form();
      setTimeout(() => {
        var my_url = `{{URL::to('sales.download_sale/')}}/${response.sale.id}`;
        window.open(my_url, '_self');
      }, 200);
    })
    .fail(function(response) {
      toastr_msg('error', '{{ config('app.name') }}', 'Ocurrió un error convirtiendo la cotización en factura', 4000);
    });
  } 
});

$(document).ready(function(){
                        
  $("#way_pay").select2({
      language: "es",
      placeholder: "Forma de pago",
      minimumResultsForSearch: 10,
      allowClear: false,
      dropdownParent: $('#modal-convert-sale .modal-content'),
      width: '100%'
  });

  $("#method_pay").select2({
      language: "es",
      placeholder: "Método de pago",
      minimumResultsForSearch: 10,
      allowClear: false,
      dropdownParent: $('#modal-convert-sale .modal-content'),
      width: '100%'
  });

  $("#condition_pay").select2({
      language: "es",
      placeholder: "Condiciones de pago",
      minimumResultsForSearch: 10,
      allowClear: false,
      dropdownParent: $('#modal-convert-sale .modal-content'),
      width: '100%'
  });
});
</script>
