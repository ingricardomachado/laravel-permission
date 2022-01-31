@extends('adminlte::page')

@section('title', 'Dashboard')

@section('plugins.Select2', true)
@section('plugins.KartikFileinput', true)


@section('content_header')
@stop

@section('content')

<h2>Hola! {{ $subscriber->bussines_name }}</h2>
<h3>Mis permisos son</h3>
<table class="table table-hover">
  <thead>
    <tr>
      <th>Permiso</th>
    </tr>
  </thead>
  <tbody>
    @foreach(Auth::user()->getPermissionsViaRoles() as $permit)
    <tr>
      <td>{{ $permit->name }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<!-- Modal Completar Registro Suscriptor -->
<div class="modal fade" id="modal-register-subscriber">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-thumbs-up"></i> ¡Bienvenido {{ $subscriber->user->bussines_name }}!</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="POST" id="form_register" role="form"  enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
            <div class="row">
                <div class="form-group col-sm-12">
                  <b>Te invitamos a culminar el proceso de registro...</b>
                </div>            
                <div class="form-group col-sm-12">
					<label>Logo </label><small> (Sólo formatos jpg, png. Máx. 2Mb.) Recomendación Máx. 200px por 200px</small>
					<input id="logo" name="logo" type="file">
                </div>
                <div class="form-group col-6">
                  <label>Estado *</label>
                  {!! Form::select('state', $states, null, ['id'=>'state', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required']) !!}
                </div>
                <div class="form-group col-6">
                  <label>Ciudad *</label>
                  {!! Form::text('city', null, ['id'=>'city', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-12">
                  <label>Dirección</label><small> Máx. 200 caracteres</small>
                  {!! Form::text('address', null, ['id'=>'address', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'200']) !!}
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" id="btn_delete_subscriber" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_full_register" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal Completar Registro Suscriptor -->

@stop

@section('footer', 'Copyright © 2021. All rights reserved.')

@section('css')

@stop

@section('js')
<script>

$("#btn_full_register").on('click', function(event) {    
    var validator = $("#form_register").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_full_register').attr('disabled', true);
        var id={{ $subscriber->id }};
        var form_data = new FormData($("#form_register")[0]);
        $.ajax({
          url:'{{URL::to("subscribers.full_register")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_full_register').attr('disabled', false);
          $('#modal-register-subscriber').modal('toggle');
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_full_register').attr('disabled', false);
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
});

$(document).ready(function(){          
                
    if({{ ($subscriber->full_registration)?0:1 }}){
      $("#modal-register-subscriber").modal("show");
    }

    $("#state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-register-subscriber .modal-content'),
        allowClear: false,
        width: '100%'
    });

	$('#logo').fileinput({
		language: 'es',
		allowedFileExtensions : ['jpg', 'jpeg', 'png'],
		previewFileIcon: "<i class='fas fa-exclamation-triangle'></i>",
		initialPreviewAsData: true,
		showUpload: false,
		showCancel: false,        
		maxFileSize: 2000,
		maxFilesNum: 1,
		overwriteInitial: true,
		progressClass: false,
		progressCompleteClass: false,
	});

	$('.kv-upload-progress').hide();            


});	
</script>
@stop