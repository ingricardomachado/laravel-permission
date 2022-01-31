        <input type="hidden" name="hdd_contact_index" id="hdd_contact_index" class="form-control" value="">        
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-truck" aria-hidden="true"></i> {{ ($supplier->id) ? "Modificar Proveedor": "Registrar Proveedor" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row pre-scrollable" style="max-height: 70vh">                            
            <form name="form_supplier" id="form_supplier" class="input-group">    
                <div class="form-group col-sm-6">
                    <label>Nombre comercial *</label>
                    {!! Form::text('name', $supplier->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Giro *</label>
                  {{ Form::select('target', $targets, ($supplier->id)?$supplier->target_id:0, ['id'=>'target', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Teléfono/celular *</label>
                    <input type="text" name="phone" id="phone" value="{{ $supplier->phone }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="8" maxlength="11" required/>
                </div>
                <div class="form-group col-sm-6">
                    <label>Correo</label>
                    {!! Form::email('email', $supplier->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'50']) !!}
                </div>
                <div class="form-group col-sm-6">  
                  <label>País *</label>
                  {{ Form::select('country', $countries, ($supplier->id)?$supplier->country_id:$subscriber->country_id, ['id'=>'country', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Estado *</label>
                  {{ Form::select('state', $states, ($supplier->id)?$supplier->state_id:$subscriber->state_id, ['id'=>'state', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Dirección</label>
                    {!! Form::text('address', $supplier->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Ciudad *</label>
                    {!! Form::text('city', ($supplier->id)?$supplier->city:$subscriber->city, ['id'=>'city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Código Postal</label>
                    <input type="text" name="zipcode" id="zipcode" class="form-control" value="{{ $supplier->zipcode }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" maxlength="50" pattern="" title="">
                </div>
                <div class="form-group col-sm-6">
                    <label>Ubicación</label>
                    {!! Form::text('location', $supplier->location, ['id'=>'location', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Página/portal web</label>
                    {!! Form::text('urls', $supplier->urls, ['id'=>'urls', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'500']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Cuentas bancarias</label>
                    {!! Form::textarea('bank_accounts', $supplier->bank_accounts, ['id'=>'bank_accounts', 'class'=>'form-control', 'rows'=>'2', 'placeholder'=>'', 'maxlength'=>'500']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Nombre de Facturación</label>
                    {!! Form::text('bussines_name', $supplier->bussines_name, ['id'=>'bussines_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Número de Facturación</label>
                    {!! Form::text('rfc', $supplier->rfc, ['id'=>'rfc', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'15']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Domicilio de Facturación</label>
                    {!! Form::text('bussines_address', $supplier->bussines_address, ['id'=>'bussines_address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Notas</label>
                    {!! Form::textarea('notes', $supplier->notes, ['id'=>'notes', 'class'=>'form-control', 'rows'=>'2', 'placeholder'=>'', 'maxlength'=>'500']) !!}
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
                    <span id="contacts_supplier"></span>
                </div>
                </form>
                <!-- Zona CONTACTOS -->

            <form name="form_documents" id="form_documents" class="input-group">
                <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                {!! Form::hidden('subscriber_id', $subscriber->id, ['id'=>'subscriber_id']) !!}
                {!! Form::hidden('supplier_id', ($supplier->id)?$supplier->id:0, ['id'=>'supplier_id']) !!}
                @if($supplier->id)                
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
                <div class="form-group col-sm-12" style="display:{{ ($supplier->documents()->count()>0)?'solid':'none' }}">
                    @foreach($supplier->documents()->get() as $document)
                        <div class="supplier-document">
                            <a href="{{ route('supplier_documents.download', $document->id) }}" title="Click para descargar">{{ $document->file_name }}</a> <a href="#" class="href-delete-document" data-id="{{ $document->id }}" title="Eliminar documento" onclick="document_delete({{ $document->id }})"><i class="far fa-trash-alt"></i></a>
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
        <div class="modal-footer">
            <button type="button" id="btn_submit_supplier" onclick="supplier_CRUD({{ ($supplier->id)?$supplier->id:0 }})" class="btn btn-sm btn-primary" {{ ($supplier->contacts()->count()==0)?'disabled':'' }}>Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
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
    url: `{{URL::to("suppliers.load_contacts")}}`,
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
    $('#contacts_supplier').html(response);
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
    $('#btn_submit_supplier').attr('disabled', (array_names.length>0)?false:true);
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

$("body").on("click",".href-delete-document",function(){
    //document_delete($(this).data("id"));    
    $(this).parents(".supplier-document").remove();
});

function document_delete(id){  
  $.ajax({
      url: `{{URL::to("supplier_documents")}}/${id}`,
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
        dropdownParent: $('#modal-supplier .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $("#country").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-supplier .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $("#state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-supplier .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $('#name').focus();
});
</script>