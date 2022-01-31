   
    <form action="{{url('subscribers/'.$subscriber->id)}}" id="form_subscriber" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('subscriber_id', ($subscriber->id)?$subscriber->id:0, ['id'=>'subscriber_id']) !!}
        @if($subscriber->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($subscriber->id) ? "Modificar Suscriptor": "Registrar Suscriptor" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">                            
                <div class="form-group col-sm-6">
                    <label>Empresa *</label>
                    {!! Form::text('name', $subscriber->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Nombre Comercial *</label>   <small>Razón Social</small>
                    {!! Form::text('bussines_name', $subscriber->bussines_name, ['id'=>'bussines_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Nombre contacto *</label>
                    {!! Form::text('first_name', $subscriber->first_name, ['id'=>'first_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Apellido contacto *</label>
                    {!! Form::text('last_name', $subscriber->last_name, ['id'=>'last_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>RFC *</label>
                    {!! Form::text('rfc', $subscriber->rfc, ['id'=>'rfc', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'minlength'=>'10', 'maxlength'=>'15', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">  
                  <label>País *</label>
                  {{ Form::select('country', $countries, ($subscriber->id)?$subscriber->country_id:1, ['id'=>'country', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-4">  
                  <label>Estado *</label>
                  {{ Form::select('state', $states, $subscriber->state_id, ['id'=>'state', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-4">
                    <label>Ciudad *</label>
                    {!! Form::text('city', $subscriber->city, ['id'=>'city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Dirección *</label>
                    {!! Form::text('address', $subscriber->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label id="label_email">Correo *</label>
                    {!! Form::email('email', $subscriber->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50', ($subscriber->user_id)?true:false]) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Celular</label>
                    <input type="text" name="cell" id="cell" value="{{ $subscriber->cell }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="10" maxlength="10"/>
                </div>
                <div class="form-group col-sm-4">
                    <label>Teléfono</label>
                    <input type="text" name="phone" id="phone" value="{{ $subscriber->phone }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="10" maxlength="10"/>
                </div>
                @if($subscriber->user_id)
                    <div class="form-group col-sm-12" id="div_change_password">                
                        <div class="icheck-primary d-inline">
                            {!! Form::checkbox('change_password', null, false, ['id'=>'change_password']) !!}
                            <label for="change_password">Cambiar contraseña</label>
                        </div>
                    </div>
                @endif
                <div class="form-group col-sm-6" id='div_password' style='display:{{ ($subscriber->id)?'none':'solid' }};'>
                    <label>Contraseña *</label><small> De 6 a 15 caracteres y 1 número.</small>
                    <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="6" maxlength="15" required>
                </div>
                <div class="form-group col-sm-6" id='div_password_confirmation' style='display:{{ ($subscriber->id)?'none':'solid' }};'>
                    <label>Confirmar Contraseña *</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="" minlength="6" maxlength="15" required>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="subscriber_CRUD({{ ($subscriber->id)?$subscriber->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>

$("#country").change( event => {
  url = `{{URL::to('countries/')}}/${event.target.value}/states`;                    
  $.get(url, function( response){
    $("#state").empty();
    response.forEach(element => {
      $("#state").append(`<option value=${element.id}> ${element.name} </option>`);
    });
    $('#state').val(null).trigger('change');
  });
});

$('#change_password').on('change', function(event) { 
  if(event.target.checked){
    $('#div_password').show();
    $('#div_password_confirmation').show();
  }else{
    $('#div_password').hide();
    $('#div_password_confirmation').hide();
  }  
});

$(document).ready(function() {
       
    $("#country").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-subscriber .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $("#state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-subscriber .modal-content'),
        allowClear: false,
        width: '100%'
    });

$.validator.addMethod(
    "regex",
    function(value, element, regexp) {
        var re = new RegExp(regexp);
        return this.optional(element) || re.test(value);
    },
    "Please check your input."
);

$("#form_subscriber").validate({            
    rules: {
        password:{
            required: true,
            minlength: 6,
            maxlength: 15,
            regex: /^(?:[0-9]+[a-z]|[a-z]+[0-9])[a-z0-9]*$/i
        },
    },
    messages: {
        password:{
            regex: 'La contraseña debe contener de 6 y 15 caracteres y al menos 1 número'
        },            
    },
    submitHandler: function(form) {
        form.submit();
    }        
});

});
</script>