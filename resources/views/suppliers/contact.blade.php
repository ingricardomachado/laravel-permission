<form action="" id="form_contact" method="POST">
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    {!! Form::hidden('supplier_id', $supplier->id, ['id'=>'supplier_id']) !!}
    @if($contact->id)                
        {{ Form::hidden ('_method', 'PUT') }}
    @endif
    <div class="modal-header">
      <h5 class="modal-title"><i class="fas fa-user"></i> {{ ($contact->id) ? "Modificar Contacto": "Registrar Contacto" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
        <div class="row">                            
            <div class="form-group col-sm-6">
                <label>Nombre *</label>
                {!! Form::text('name', $contact->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
            </div>
            <div class="form-group col-sm-6">
                <label>Título</label>
                {!! Form::text('position', $contact->position, ['id'=>'position', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
            </div>
            <div class="form-group col-sm-6">
                <label>Celular *</label>
                <input type="text" name="phone" id="phone" value="{{ $contact->phone }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="8" maxlength="11" required/>
            </div>
            <div class="form-group col-sm-6">
                <label>Correo</label>
                {!! Form::email('email', $contact->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50']) !!}
            </div>
            @if(!$contact->id || ($contact->id &&!$contact->main))
                <div class="form-group col-sm-12">
                    <div class="icheck-primary d-inline">
                        {!! Form::checkbox('chk_main', null, false, ['id'=>'chk_main']) !!}
                        <label for="chk_main">Convertir en principal.</label><small> Sólo puede haber un contacto principal.</small>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        @if($contact->id && !$contact->main)
            <button type="button" id="btn_delete_contact" onclick="contact_delete({{ $contact->id }})" class="btn btn-sm btn-danger">Eliminar</button>
        @endif
        <button type="button" id="btn_contact" onclick="contact_CRUD({{ ($contact->id)?$contact->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
    </div>
</form>
<script>

function contact_delete(id){  
  $.ajax({
      url: `{{URL::to("supplier_contacts")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-contact').modal('toggle');
      $('#suppliers-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      $('#modal-contact').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function contact_CRUD(id){
        
    var validator = $("#form_contact").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_contact').attr('disabled', true);
        var form_data = new FormData($("#form_contact")[0]);
        form_data.append('main', $('#chk_main').is(":checked")?1:0);
        $.ajax({
          url:(id==0)?'{{URL::to("supplier_contacts")}}':'{{URL::to("supplier_contacts")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_contact').attr('disabled', false);
          $('#modal-contact').modal('toggle');
          $('#suppliers-table').DataTable().draw(); 
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_contact').attr('disabled', false);
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

$(document).ready(function() {            

});
</script>