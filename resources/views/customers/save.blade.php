   
<div class="container lst">
    
        <input type="hidden" name="hdd_contact_index" id="hdd_contact_index" class="form-control" value="">
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($customer->id) ? "Modificar Cliente": "Registrar Cliente" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="card-header p-0 pt-1">
                <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill" href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home" aria-selected="true">Datos generales</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill" href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile" aria-selected="false">Más información</a>
                  </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-one-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                    <div class="row pre-scrollable" style="max-height: 55vh">
                        <form name="form_customer" id="form_customer" class="input-group">
                        @if($customer->id)
                          @if($customer->type=='P' && $customer->sucursales->count()==0)
                            <div class="form-group col-sm-8">
                                <div class="icheck-primary d-inline">
                                    {!! Form::checkbox('sucursal', null, false, ['id'=>'sucursal']) !!}
                                    <label for="sucursal">Pasar cliente a sucursal</label>
                                </div>
                            </div>
                          @elseif($customer->type=='S')
                            <div class="form-group col-sm-8">
                                <div class="icheck-primary d-inline">
                                    {!! Form::checkbox('change_parent', null, false, ['id'=>'change_parent']) !!}
                                    <label for="change_parent">Pasar cliente a principal</label>
                                </div>
                            </div>
                          @endif
                        @else
                            <div class="form-group col-sm-8">
                                <div class="icheck-primary d-inline">
                                    {!! Form::checkbox('sucursal', null, false, ['id'=>'sucursal']) !!}
                                    <label for="sucursal">Cliente Sucursal</label><small> Marca la casilla si tu cliente es sucursal.</small>
                                </div>
                            </div>
                        @endif

                        @if(false && !$customer->id || (!$customer->user_id && $customer->type=='P'))
                            <div class="form-group col-sm-12" id="div_create_user">
                                <div class="icheck-primary d-inline">
                                    {!! Form::checkbox('create_user', null, false, ['id'=>'create_user']) !!}
                                    <label for="create_user">Crear acceso para cliente</label><small> Crea usuario y contraseña para cliente</small>
                                </div>
                            </div>
                        @endif
                        <div class="form-group col-sm-12" id="div_parents" style='display:none'>
                          {{ Form::select('parent', $parents, $customer->parent_id, ['id'=>'parent', 'class'=>'select2 form-control', 'placeholder'=>'', 'required'])}}
                        </div>                        
                        <div class="form-group col-sm-6">
                            <label>Nombre Comercial *</label>
                            {!! Form::text('name', $customer->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-6">  
                          <label>Giro *</label>
                          {{ Form::select('target', $targets, ($customer->id)?$customer->target_id:0, ['id'=>'target', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Teléfono/celular</label>
                            <input type="text" name="phone" id="phone" value="{{ $customer->phone }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="8" maxlength="11"/>
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Correo</label>
                            {!! Form::email('email', $customer->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Calle *</label>
                            {!! Form::text('street', $customer->street, ['id'=>'street', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Número *</label>
                            {!! Form::text('street_number', $customer->street_number, ['id'=>'street_number', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Colonia</label>
                            {!! Form::text('neighborhood', $customer->neighborhood, ['id'=>'neighborhood', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Código Postal</label>
                            <input type="text" name="zipcode" id="zipcode" class="form-control" value="{{ $customer->zipcode }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="50" pattern="" title="">
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Ciudad *</label>
                            {!! Form::text('city', ($customer->id)?$customer->city:$subscriber->city, ['id'=>'city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-6">  
                          <label>País *</label>
                          {{ Form::select('country', $countries, ($customer->id)?$customer->country_id:$subscriber->country_id, ['id'=>'country', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                        </div>
                        <div class="form-group col-sm-6">  
                          <label>Estado *</label>
                          {{ Form::select('state', $states, ($customer->id)?$customer->state_id:$subscriber->state_id, ['id'=>'state', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                        </div>
                        <div class="form-group col-sm-6 form-control-border">
                            <label>Ubicación</label>
                            {!! Form::text('address', $customer->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Notas</label>
                            {!! Form::textarea('notes', $customer->notes, ['id'=>'notes', 'class'=>'form-control', 'rows'=>'2', 'placeholder'=>'', 'maxlength'=>'500']) !!}
                        </div>
                        @if($customer->user_id)
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
                        </form>
                        
                        <!-- Zona CONTACTOS -->
                        <div class="form-group col-sm-12">
                            <b>CONTACTOS</b><small> Debe agregar al menos un contacto. El primer contacto agregado será considerado el principal.</small>
                        </div>
                        <form name="form_contact" id="form_contact" class="input-group">
                        <div class="form-group col-sm-4">
                            <label>Nombre *</label>
                            {!! Form::text('contact_name', null, ['id'=>'contact_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                        </div>
                        <div class="form-group col-sm-4">
                            <label>Título</label>
                            {!! Form::text('contact_occupation', null, ['id'=>'contact_occupation', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                        </div>
                        <div class="form-group col-sm-4">
                            <label>Area/Puesto</label>
                            {!! Form::text('contact_position', null, ['id'=>'contact_position', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                        </div>
                        <div class="form-group col-sm-4">
                            <label>Celular *</label>
                            <input type="text" name="contact_phone" id="contact_phone" value="" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="8" maxlength="11" required/>
                        </div>
                        <div class="form-group col-sm-4">
                            <label>Correo</label>
                            {!! Form::email('contact_email', null, ['id'=>'contact_email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                        </div>
                        <div class="form-group col-sm-4" style="margin-top:8mm">
                            <button type="button" id="btn_add_contact" class="btn btn-primary btn-block"> Agregar</button>
                            <button type="button" id="btn_update_contact" class="btn btn-primary btn-block" style="display:none"> Actualizar</button>
                        </div>
                        <div class="form-group col-sm-12">
                            <span id="contacts_customer"></span>
                        </div>
                        </form>
                        <!-- Zona CONTACTOS -->
                        
                    <form name="form_documents" id="form_documents" class="input-group">
                        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                        {!! Form::hidden('subscriber_id', $subscriber->id, ['id'=>'subscriber_id']) !!}
                        {!! Form::hidden('customer_id', ($customer->id)?$customer->id:0, ['id'=>'customer_id']) !!}
                        @if($customer->id)                
                            {{ Form::hidden ('_method', 'PUT') }}
                        @endif
                        <div class="form-group col-sm-12">
                            <label>Documentos</label><small> (Sólo formatos pdf, xls, xlsx, doc, docs, odt, ods. Máx. 10Mb.)</small>
                            <div class="input-group hdtuto control-group lst increment" >
                              <input type="file" name="filenames[]" class="myfrm form-control">
                              <div class="input-group-btn">
                                <button class="btn btn-primary btn-clone" type="button"><i class="fas fa-file-medical"></i> Agregar mas documentos</button>
                              </div>
                            </div>
                        </div>
                        <div class="form-group col-sm-12" style="display:{{ ($customer->documents()->count()>0)?'solid':'none' }}">
                            @foreach($customer->documents()->get() as $document)
                                <div class="customer-document">
                                    <a href="{{ route('customer_documents.download', $document->id) }}" title="Click para descargar">{{ $document->file_name }}</a> <a href="#" class="href-delete-document" data-id="{{ $document->id }}" title="Eliminar documento" onclick="document_delete({{ $document->id }})"><i class="far fa-trash-alt"></i></a>
                                </div>                        
                            @endforeach
                        </div>

                        <div class="clone" style="display:none">
                          <div class="hdtuto control-group lst input-group" style="margin-top:10px">
                            <input type="file" name="filenames[]" class="myfrm form-control">
                            <div class="input-group-btn">
                              <button class="btn btn-danger" type="button"><i class="far fa-trash-alt"></i></button>
                            </div>
                          </div>
                        </div>
                    </form>            
                    </div>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel" aria-labelledby="custom-tabs-one-profile-tab">
                    <div class="row pre-scrollable" style="max-height: 55vh">
                        <div class="form-group col-sm-6">
                            <label>Página/portal web</label>
                            {!! Form::text('urls', $customer->urls, ['id'=>'urls', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Nombre de Facturación</label>
                            {!! Form::text('bussines_name', $customer->bussines_name, ['id'=>'bussines_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Número de Facturación</label>
                            {!! Form::text('rfc', $customer->rfc, ['id'=>'rfc', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'15']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Domicilio de Facturación</label>
                            {!! Form::text('bussines_address', $customer->bussines_address, ['id'=>'bussines_address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Calle de envío</label>
                            {!! Form::text('shipping_street', $customer->shipping_street, ['id'=>'shipping_street', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Número de envío</label>
                            {!! Form::text('shipping_number', $customer->shipping_number, ['id'=>'shipping_number', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Colonia de envío</label>
                            {!! Form::text('shipping_neighborhood', $customer->shipping_neighborhood, ['id'=>'shipping_neighborhood', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Codigo Postal de envío</label>
                            <input type="text" name="shipping_zipcode" id="shipping_zipcode" class="form-control" value="{{ $customer->shipping_zipcode }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="50" pattern="" title="">
                        </div>
                        <div class="form-group col-sm-6">  
                          <label>País de envío</label>
                          {{ Form::select('shipping_country', $countries, ($customer->id)?$customer->shipping_country_id:$subscriber->country_id, ['id'=>'shipping_country', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                        </div>
                        <div class="form-group col-sm-6">  
                          <label>Estado de envío</label>
                          {{ Form::select('shipping_state', $shipping_states, ($customer->id)?$customer->shipping_state_id:$subscriber->state_id, ['id'=>'shipping_state', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Ciudad de envío</label>
                            {!! Form::text('shipping_city', $customer->shipping_city, ['id'=>'shipping_city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                        </div>
                        <div class="form-group col-sm-6">
                            <label>Descuento</label>
                            <input type="number" name="discount" id="discount" class="form-control" value="{{ $customer->discount }}" min="1" max="100" step="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');" title="">
                        </div>
                    </div>
                  </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit_customer" onclick="customer_CRUD({{ ($customer->id)?$customer->id:0 }})" class="btn btn-sm btn-primary" {{ ($customer->contacts()->count()==0)?'disabled':'' }}>Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
</div>
<script>

var array_names={!! $array_names !!};
var array_occupations={!! $array_occupations !!};
var array_positions={!! $array_positions !!};
var array_phones={!! $array_phones !!};
var array_emails={!! $array_emails !!};
var array_mains={!! $array_mains !!};
var max_contacts=20;

function load_contacts(){
  $.ajax({
    url: `{{URL::to("customers.load_contacts")}}`,
    type: 'POST',
    data: {
      _token: "{{ csrf_token() }}", 
      array_names:array_names,
      array_occupations:array_occupations,
      array_positions:array_positions,
      array_phones:array_phones,
      array_emails:array_emails,
      array_mains:array_mains,
    },
  })
  .done(function(response) {
    $('#contacts_customer').html(response);
  })
  .fail(function() {
    //
  });
}

$("#btn_add_contact").on('click', function(event) {
  add_contact();
});

function add_contact(){
  //var validator = $("#form_contact").validate();
  //formulario_validado = validator.form();
  if(true){
    array_mains.push((array_names.length==0)?"1":"0")
    array_names.push($('#contact_name').val());
    array_positions.push($('#contact_position').val());
    array_occupations.push($('#contact_occupation').val());
    array_phones.push($('#contact_phone').val());
    array_emails.push($('#contact_email').val());
    clear_form_contact();
    load_contacts();
    $('#btn_add_contact').attr('disabled', (array_names.length<max_contacts)?false:true);
    $('#btn_submit_customer').attr('disabled', (array_names.length>0)?false:true);
  }    
}

function edit_contact(index) {
    $('#hdd_contact_index').val(index);
    $('#btn_add_contact').hide();
    $('#btn_update_contact').show();
    $('#contact_name').val(array_names[index]);
    $('#contact_occupation').val(array_occupations[index]);
    $('#contact_position').val(array_positions[index]);
    $('#contact_phone').val(array_phones[index]);
    $('#contact_email').val(array_emails[index]);
};

function remove_contact(index) {
  array_names.splice(index, 1);
  array_positions.splice(index, 1);
  array_occupations.splice(index, 1);
  array_phones.splice(index, 1);
  array_emails.splice(index, 1);
  array_mains.splice(index, 1);
  load_contacts();
  $('#btn_add_contact').attr('disabled', (array_names.length<max_contacts)?false:true);
};

function clear_form_contact(){
  $('#contact_name').val('');
  $('#contact_occupation').val('');
  $('#contact_position').val('');
  $('#contact_phone').val('');
  $('#contact_email').val('');
}

$("#btn_update_contact").on('click', function(event) {
  update_contact();
  $('#btn_add_contact').show();
  $('#btn_update_contact').hide();
});

function update_contact(){
  //var validator = $("#form_header").validate();
  //formulario_validado = validator.form();
  if(true){
    var index=$('#hdd_contact_index').val();
    array_names[index]=$('#contact_name').val();
    array_occupations[index]=$('#contact_occupation').val();
    array_positions[index]=$('#contact_position').val();
    array_phones[index]=$('#contact_phone').val();
    array_emails[index]=$('#contact_email').val();
    clear_form_contact();
    load_contacts();
    $('#btn_add_product').attr('disabled', (array_names.length<max_contacts)?false:true);
  }
}

$('#create_user').on('change', function(event) { 
    if(event.target.checked){
        $('#label_email').html('Correo *');
        $('#div_password').show();
        $('#div_password_confirmation').show();
        $('#email').attr('required', true);
    }else{
        $('#label_email').html('Correo');
        $('#div_password').hide();
        $('#div_password_confirmation').hide();
        $('#email').attr('required', false);
    }
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

$('#sucursal').on('change', function(event) { 
    if(event.target.checked){
        $('#div_parents').show();
        $('#div_create_user').hide();
    }else{
        $('#div_parents').hide();
        $('#div_create_user').show();
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
      
$("#shipping_country").change( event => {
  url = `{{URL::to('countries/')}}/${event.target.value}/states`;                    
  $.get(url, function( response){
    $("#shipping_state").empty();
    response.forEach(element => {
      $("#shipping_state").append(`<option value=${element.id}> ${element.name} </option>`);
    });
    $('#shipping_state').val(null).trigger('change');
  });
});

$("body").on("click",".href-delete-document",function(){
    //document_delete($(this).data("id"));    
    $(this).parents(".customer-document").remove();
});

function document_delete(id){  
  $.ajax({
      url: `{{URL::to("customer_documents")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {      
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

$(document).ready(function() {
            
    load_contacts();

    $(".btn-clone").click(function(){
        var lsthmtl = $(".clone").html();
        $(".increment").after(lsthmtl);
    });

    $("body").on("click",".btn-danger",function(){
        $(this).parents(".hdtuto").remove();
    });
    
    $("#target").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-customer .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $("#country").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-customer .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $("#shipping_country").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-customer .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $("#shipping_state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-customer .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $("#parent").select2({
        language: "es",
        placeholder: "Seleccione el cliente principal",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-customer .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $('#name').focus();
});

</script>