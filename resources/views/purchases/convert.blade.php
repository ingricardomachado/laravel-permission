<form action="" method="POST" id="form_convert">
  <div class="modal-header">
    <h4 class="modal-title"><i class="fas fa-store"></i> <strong>Convertir en Compra</strong></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>      
  <div class="modal-body">
      <div class="form-group col-sm-12">
        <b>Fecha de Compra</b> {{ Carbon\Carbon::now()->format('d/m/Y') }}<br>
        <b>Proveedor</b> {{ $purchase->supplier->name }}<br>
        <b>Orden de compra</b> {{ $purchase->folio }}<br>
        <b>Monto</b> {{ money_fmt($purchase->total) }}
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
              <small> Marca la casilla si quieres enviar la compra por correo al proveedor.</small>
          </div>
          <div id="div_to" style="display:none;margin-top: 2mm;">
            {!! Form::email('convert_to', ($purchase->supplier_id)?$purchase->supplier->email:'', ['id'=>'convert_to', 'class'=>'form-control', 'placeholder'=>'Correo a enviar...', 'title'=>'', 'required']) !!}
          </div>
      </div>
  </div>
  <div class="modal-footer">
      <button type="button" id="btn_close" class="btn grey btn-outline-secondary" data-dismiss="modal">Cerrar</button>        
      <button type="button" id="btn_convert_purchase" class="btn btn-outline-primary">Convertir</button>
  </div>
</form>
<script>

$('#convert_custom_folio').on('change', function(event) { 
  (event.target.checked)?$('#div_convert_folio').show():$('#div_convert_folio').hide();
});

$('#convert_send_email').on('change', function(event) { 
  (event.target.checked)?$('#div_to').show():$('#div_to').hide();
});

$("#btn_convert_purchase").on('click', function(event) {    
  var validator = $("#form_convert").validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    $(this).attr('disabled', true);
    var id={{ $purchase->id }};
    $.ajax({
        url: '{{URL::to("purchases.convert")}}/'+id,
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          custom_folio:($('#convert_custom_folio').is(":checked"))?1:0,
          folio:$('#convert_folio').val(),
          send_email:($('#convert_send_email').is(":checked"))?1:0,
          to:$('#convert_to').val() 
        },
    })
    .done(function(response) {
      $('#btn_convert_purchase').attr('disabled', false);
      $('#modal-convert-purchase').modal('toggle');
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
      reset_form();
      setTimeout(() => {
        var my_url = `{{URL::to('purchases.download_purchase/')}}/${response.purchase.id}`;
        window.open(my_url, '_self');
      }, 200);
    })
    .fail(function(response) {
      toastr_msg('error', '{{ config('app.name') }}', 'Ocurrió un error convirtiendo la orden de compra en compra', 4000);
    });
  } 
});

</script>
