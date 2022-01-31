   
    <form action="{{url('employees/'.$employee->id)}}" id="form_employee" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('subscriber_id', $subscriber->id, ['id'=>'subscriber_id']) !!}
        {!! Form::hidden('employee_id', ($employee->id)?$employee->id:0, ['id'=>'employee_id']) !!}
        @if($employee->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($employee->id) ? "Modificar Empleado": "Registrar Empleado" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">                            
                <div class="form-group col-sm-4">
                    <label>Nombre *</label>
                    {!! Form::text('first_name', $employee->first_name, ['id'=>'first_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Apellido *</label>
                    {!! Form::text('last_name', $employee->last_name, ['id'=>'last_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">  
                  <label>Rol *</label>
                  {{ Form::select('role', $roles, ($employee->id)?$employee->user->roles->first()->name:null, ['id'=>'role', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-4">
                    <label id="label_email">Correo *</label>
                    {!! Form::email('email', $employee->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Celular *</label>
                    <input type="text" name="cell" id="cell" value="{{ $employee->cell }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="10" maxlength="10" required/>
                </div>
                <div class="form-group col-sm-4">  
                  <label>País *</label>
                  {{ Form::select('country', $countries, ($employee->id)?$employee->country_id:$subscriber->country_id, ['id'=>'country', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-4">  
                  <label>Estado *</label>
                  {{ Form::select('state', $states, ($employee->id)?$employee->state_id:$subscriber->state_id, ['id'=>'state', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-4">
                    <label>Ciudad *</label>
                    {!! Form::text('city', ($employee->id)?$employee->city:$subscriber->city, ['id'=>'city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                    <label>Teléfono</label>
                    <input type="text" name="phone" id="phone" value="{{ $employee->phone }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="10" maxlength="10"/>
                </div>                
                <div class="form-group col-sm-6">
                    <label>Dirección *</label>
                    {!! Form::text('address', $employee->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Campo 1</label>
                    {!! Form::text('field1', $employee->field1, ['id'=>'field1', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'200']) !!}
                </div>
                @if($employee->user_id)
                    <div class="form-group col-sm-12" id="div_change_password">
                        <div class="icheck-primary d-inline">
                            {!! Form::checkbox('change_password', null, false, ['id'=>'change_password']) !!}
                            <label for="change_password">Cambiar contraseña</label>
                        </div>
                    </div>
                @endif
                <div class="form-group col-sm-6" id='div_password' style='display:none;'>
                    <label>Contraseña *</label><small> Min 6 caracteres.</small>
                    <input type="password" class="form-control" name="password" id="password" placeholder="" minlength="6" maxlength="15" required>
                </div>
                <div class="form-group col-sm-6" id='div_password_confirmation' style='display:none;'>
                    <label>Confirmar Contraseña *</label>
                    <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="" minlength="6" maxlength="15" required>
                </div>
            
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="employee_CRUD({{ ($employee->id)?$employee->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>
$('#change_password').on('change', function(event) { 
    if(event.target.checked){
        $('#div_password').show();
        $('#div_password_confirmation').show();
    }else{
        $('#div_password').hide();
        $('#div_password_confirmation').hide();
    }
});

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

$(document).ready(function() {
        
    $("#role").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-employee .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $("#country").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-employee .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $("#state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-employee .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $('#name').focus();
});

</script>