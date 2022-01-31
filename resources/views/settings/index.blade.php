@extends('adminlte::page')

@section('plugins.Select2', true)
@section('plugins.KartikFileinput', true)

@section('content_header')
@stop

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
    <i class="fas fa-cogs" aria-hidden="true"></i> Configuraciones generales <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h3>
  </div>
  <div class="card-body">
    <form action="#" id="form" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
      @if($subscriber->id>0)
          <input type="hidden" name="_method" value="PUT" />
      @endif
      <div class="row">
        <div class="col-sm-6">
          <div class="form-group">
              <label>Logo </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) Recomendación Máx. 200px por 200px</small>
              <input id="logo" name="logo" class="file" type="file">
          </div>
        </div>
        <div class="col-sm-6">
          <div class="form-group">
              <label>Sello </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) Recomendación Máx. 200px por 200px</small>
              <input id="stamp" name="stamp" class="file" type="file">
          </div>
        </div>
        <div class="form-group col-sm-4">
            <label>Nombre *</label>
            {!! Form::text('first_name', $subscriber->first_name, ['id'=>'first_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
        </div>
        <div class="form-group col-sm-4">
            <label>Apellido *</label>
            {!! Form::text('last_name', $subscriber->last_name, ['id'=>'last_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
        </div>
        <div class="form-group col-sm-4">
            <label>Nombre Comercial</label> 
            {!! Form::text('name', $subscriber->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
        </div>                     
        <div class="form-group col-sm-4">
            <label>Nombre Fiscal</label>
            {!! Form::text('bussines_name', $subscriber->bussines_name, ['id'=>'bussines_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
        </div> 
        <div class="form-group col-sm-4">
            <label>RFC</label>
            {!! Form::text('rfc', $subscriber->rfc, ['id'=>'rfc', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'minlength'=>'10', 'maxlength'=>'15']) !!}
        </div>
        <div class="form-group col-sm-4">
            <label>Teléfono</label>
            <input type="text" name="phone" id="phone" value="{{ $subscriber->phone }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="10" maxlength="10"/>
        </div>
        <div class="form-group col-sm-4">
            <label>Correo electrónico *</label>
            {!! Form::email('email', $subscriber->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'empresa@dominio.com', 'maxlength'=>'50', 'required']) !!}
        </div>
        <div class="form-group col-sm-4">
            <label>Símbolo de moneda *</label>
            {!! Form::text('coin', $subscriber->coin, ['id'=>'coin', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Ej. MXN', 'maxlength'=>'10', 'required']) !!}
        </div>                            
        <div class="form-group col-sm-4">
            <label>Nombre de moneda *</label>
            {!! Form::text('coin_name', $subscriber->coin_name, ['id'=>'coin_name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Ej. Pesos Mexicanos', 'maxlength'=>'20']) !!}
        </div>
        <div class="form-group col-sm-4">
            <label>Formato de moneda *</label>
            {!! Form::select('money_format', ['PC2'=>'1.000,00', 'CP2' => '1,000.00'], $subscriber->money_format, ['id'=>'money_format', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required']) !!}
        </div>
        <div class="form-group col-sm-4">  
          <label>País *</label>
          {{ Form::select('country', $countries, ($subscriber->id)?$subscriber->country_id:1, ['id'=>'country', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
        </div>
        <div class="form-group col-sm-4">  
          <label>Estado *</label>
          {{ Form::select('state', $states, $subscriber->state_id, ['id'=>'state', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
        </div>
        <div class="form-group col-sm-4">
            <label>Ciudad *</label>
            {!! Form::text('city', $subscriber->city, ['id'=>'city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'50', 'required']) !!}
        </div>
        <div class="form-group col-sm-8">
            <label>Dirección *</label>
            {!! Form::text('address', $subscriber->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'200', 'required']) !!}
        </div>
        <div class="form-group col-sm-12 text-right">
            <button type="button" onclick="update({{ $subscriber->id }})" id="btn_update" class="btn btn-sm btn-primary">Guardar</button>
        </div>
      </div>
    </form>
  </div>
</div>

@stop

@section('footer', 'Copyright © 2021. All rights reserved.' )

@section('css')
  <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
  
$('#logo').fileinput({
    language: 'es',
    allowedFileExtensions : ['jpg', 'jpeg', 'png'],
    previewFileIcon: '<i class="fas fa-exclamation-triangle"></i>',
    browseLabel: '<i class="fas fa-folder-open"></i>',
    removeLabel: '<i class="fas fa-trash"></i>',
    showUpload: false,
    showCancel: false,        
    maxFileSize: 2000,
    maxFilesNum: 1,
    overwriteInitial: true,
    progressClass: true,
    progressCompleteClass: true,
    initialPreview: [
      "<img style='max-height:100px' src='{{ url(($subscriber->id)?'subscriber_logo/'.$subscriber->id:'img/no_image_available.png') }}'>"
    ]      
});

$('#stamp').fileinput({
    language: 'es',
    allowedFileExtensions : ['jpg', 'jpeg', 'png'],
    previewFileIcon: '<i class="fas fa-exclamation-triangle"></i>',
    browseLabel: '<i class="fas fa-folder-open"></i>',
    removeLabel: '<i class="fas fa-trash"></i>',
    showUpload: false,
    showCancel: false,        
    maxFileSize: 2000,
    maxFilesNum: 1,
    overwriteInitial: true,
    progressClass: true,
    progressCompleteClass: true,
    initialPreview: [
      "<img style='max-height:100px' src='{{ url(($subscriber->id)?'subscriber_stamp/'.$subscriber->id:'img/no_image_available.png') }}'>"
    ]      
});

$('.kv-upload-progress').hide();            
    
function update(id){
    var validator = $("#form").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
      $('#btn_update').attr('disabled',true);
      var form_data = new FormData($("#form")[0]);
      $.ajax({
        url:'{{URL::to("settings")}}/'+id,
        type:'POST',
        cache:true,
        processData: false,
        contentType: false,      
        data: form_data
      })
      .done(function(response) {
        $('#btn_update').attr('disabled',false);
        toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
      })
      .fail(function(response) {
        if(response.status == 422){
          var errorsHtml='';
          $.each(response.responseJSON.errors, function (key, value) {
            errorsHtml += '<li>' + value[0] + '</li>'; 
          });          
          toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 4000);
        }else{
          toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
        }
      });
    }
}


$(document).ready(function() {
                
  // Select2 
  $("#money_format").select2({
    language: "es",
    placeholder: "Seleccione un formato numérico",
    minimumResultsForSearch: 10,
    allowClear: false,
    width: '100%'
  });

  $("#country").select2({
      language: "es",
      placeholder: "Seleccione",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
  });
  
  $("#state").select2({
      language: "es",
      placeholder: "Seleccione",
      minimumResultsForSearch: 10,
      allowClear: false,
      width: '100%'
  });

});

</script>
@stop