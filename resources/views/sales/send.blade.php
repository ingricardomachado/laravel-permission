<form action="" method="POST" id="form_send">
  <div class="modal-header">
    <h4 class="modal-title"><i class="far fa-envelope"></i> <strong>Enviar {{ $sale->type=='C'?'Cotización':'Factura' }}</strong></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>      
  <div class="modal-body">
      <div class="form-group col-12">
        Enviar la {{ ($sale->type=='C')?'cotización':'factura' }} <b>{{ $sale->folio }}</b> del Cliente <b>{{ $sale->customer->name }}</b>.
      </div>
      <div class="form-group col-12">
        <label>Correo destino</label>
        {!! Form::email('to', $sale->customer->email, ['id'=>'to', 'class'=>'form-control', 'placeholder'=>'Correo a enviar', 'title'=>'', 'required']) !!}
      </div>
  </div>
  <div class="modal-footer">
      <button type="button" id="btn_close" class="btn grey btn-outline-secondary" data-dismiss="modal">Cerrar</button>        
      <button type="button" id="btn_send_sale" class="btn btn-outline-primary">Enviar</button>
  </div>
</form>
<script>
  $("#btn_send_sale").on('click', function(event) {    
    var validator = $("#form_send").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
      $(this).attr('disabled', true);
      var id={{ $sale->id }};
      var to=$('#to').val();
      send_email(id,to);
    } 
  });
</script>
