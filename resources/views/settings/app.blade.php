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
      <div class="row">
        <div class="col-sm-5">
          <div class="form-group">
              <label>Logo </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) 
              <br>Recomendación Máx. 200px por 200px</small>
              <input id="logo" name="logo" class="file" type="file">
          </div>
        </div>
        <div class="col-sm-7">
          <div class="row">
            <div class="form-group col-sm-8">
                <label>Nombre de la Organización *</label> <small>Razón Social</small>
                {!! Form::text('company', $setting->company, ['id'=>'company', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
            </div>                            
            <div class="form-group col-sm-4">
                <label>RFC *</label>
                {!! Form::text('NIT', $setting->NIT, ['id'=>'NIT', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'15', 'required']) !!}
            </div>
            <div class="form-group col-sm-6">
                <label>Teléfono</label>
                <input type="text" name="phone" id="phone" value="{{ $setting->phone }}" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="" minlength="10" maxlength="10"/>
            </div>
            <div class="form-group col-sm-6">
                <label>Correo electrónico *</label>
                {!! Form::email('email', $setting->email, ['id'=>'email', 'class'=>'form-control', 'placeholder'=>'empresa@dominio.com', 'maxlength'=>'50', 'required']) !!}
            </div>
            <div class="form-group col-sm-12">
                <label>Dirección *</label>
                {!! Form::text('address', $setting->address, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'200', 'required']) !!}
            </div>
            <div class="form-group col-sm-6">
                <label>Símbolo de moneda *</label>
                {!! Form::text('coin', $setting->coin, ['id'=>'coin', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'Ej. MXN', 'maxlength'=>'10', 'required']) !!}
            </div>                            
            <div class="form-group col-sm-6">
                <label>Formato de moneda *</label>
                {!! Form::select('money_format', ['PC2'=>'1.000,00', 'CP2' => '1,000.00'], $setting->money_format, ['id'=>'money_format', 'class'=>'select2_single form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required']) !!}
            </div>
          </div>
        </div>
        <div class="col-sm-12 text-right">
          <button type="button" onclick="update()" id="btn_update" class="btn btn-sm btn-primary">Guardar</button>
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
    previewFileIcon: "<i class='glyphicon glyphicon-king'></i>",
    showUpload: false,
    showCancel: false,        
    maxFileSize: 2000,
    maxFilesNum: 1,
    overwriteInitial: true,
    progressClass: true,
    progressCompleteClass: true,
    initialPreview: [
      "<img style='max-height:100px' src='{{ url(($setting->id)?'app_logo':'img/no_image_available.png') }}'>"
    ]      
});

$('.kv-upload-progress').hide();            
    
function update(){
    var validator = $("#form").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
      $('#btn_update').attr('disabled',true);
      var form_data = new FormData($("#form")[0]);
      $.ajax({
        url:'{{URL::to("settings.update_app")}}',
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

});

</script>
@stop