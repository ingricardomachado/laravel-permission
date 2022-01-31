   
    <form action="{{url('contacts/'.$contact->id)}}" id="form_contact" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('subscriber_id', ($contact->id)?$contact->subscriber_id:session('subscriber')->id, ['id'=>'subscriber_id']) !!}
        {!! Form::hidden('contact_id', ($contact->id)?$contact->id:0, ['id'=>'contact_id']) !!}
        {!! Form::hidden('full_name', null, ['id'=>'full_name']) !!}
        @if($contact->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-user" aria-hidden="true"></i> {{ ($contact->id) ? "Modificar Proveedor": "Registrar Proveedor" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row pre-scrollable" style="max-height: 70vh">                            
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('first_name', $contact->first_name, ['id'=>'first_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Apellido *</label>
                    {!! Form::text('last_name', $contact->last_name, ['id'=>'last_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Cargo *</label>
                    {!! Form::text('position', $contact->position, ['id'=>'position', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Título *</label>
                    {!! Form::text('profession', $contact->profession, ['id'=>'profession', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Tipo</label>
                    {!! Form::text('type', $contact->type, ['id'=>'type', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Celular *</label>
                    <input type="text" name="cell" id="cell" value="{{ $contact->cell }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="8" maxlength="11" required/>
                </div>
                <div class="form-group col-sm-6">
                    <label>Teléfono</label>
                    <input type="text" name="phone" id="phone" value="{{ $contact->phone }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="8" maxlength="11"/>
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo</label>
                    {!! Form::email('email', $contact->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Calle</label>
                    {!! Form::text('street', $contact->street, ['id'=>'street', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Nro</label>
                    {!! Form::text('street_number', $contact->street_number, ['id'=>'street_number', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Colonia</label>
                    {!! Form::text('neighborhood', $contact->neighborhood, ['id'=>'neighborhood', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                </div>
                <div class="form-group col-sm-6">  
                  <label>País</label>
                  {{ Form::select('country', $countries, ($contact->id)?$contact->country_id:$subscriber->country_id, ['id'=>'country', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Estado</label>
                  {{ Form::select('state', $states, ($contact->id)?$contact->state_id:$subscriber->state_id, ['id'=>'state', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Ciudad</label>
                    {!! Form::text('city', ($contact->id)?$contact->city:$subscriber->city, ['id'=>'city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Código Postal</label>
                    <input type="text" name="zipcode" id="zipcode" class="form-control" value="{{ $contact->zipcode }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="50" pattern="" title="">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="contact_CRUD({{ ($contact->id)?$contact->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>
$('#more_info').on('change', function(event) { 
  (event.target.checked)?$('#div_more_info').show():$('#div_more_info').hide();  
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
        
    $("#country").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-contact .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $("#state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-contact .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $('#first_name').focus();
});
</script>