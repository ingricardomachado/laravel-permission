<form action="" method="POST" id="form_send">
  <div class="modal-header">
    <h4 class="modal-title"><i class="far fa-envelope"></i> <strong>Enviar {{ $purchase->type=='O'?'Orden':'Compra' }}</strong></h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>      
  <div class="modal-body">
      <div class="form-group col-12">
        Enviar la {{ ($purchase->type=='O')?'orden de compra':'compra' }} <b>{{ $purchase->folio }}</b> del Proveedor <b>{{ $purchase->supplier->name }}</b>.
      </div>
      <div class="form-group col-12">
        <label>Correo destino</label>
        {!! Form::email('to', $purchase->supplier->email, ['id'=>'to', 'class'=>'form-control', 'placeholder'=>'Correo a enviar', 'title'=>'', 'required']) !!}
      </div>
  </div>
  <div class="modal-footer">
      <button type="button" id="btn_close" class="btn grey btn-outline-secondary" data-dismiss="modal">Cerrar</button>        
      <button type="button" id="btn_send_purchase" class="btn btn-outline-primary">Enviar</button>
  </div>
</form>
<script>
  $("#btn_send_purchase").on('click', function(event) {    
    var validator = $("#form_send").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
      $(this).attr('disabled', true);
      var id={{ $purchase->id }};
      var to=$('#to').val();
      send_email(id,to);
    } 
  });
</script>
