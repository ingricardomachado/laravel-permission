@extends('adminlte::page')

@section('plugins.Select2', true)
@section('plugins.ICheck', true)
@section('plugins.KartikFileinput', true)
@section('plugins.MagnificPopup', true)


<style type="text/css">
    
/* *********  media gallery  **************************** */

.img-thumbnail .image {
  height: 150px;
  overflow: hidden; }

.caption {
  padding: 9px 5px;
  background: #F7F7F7; }

.caption p {
  margin-bottom: 5px; }

.img-thumbnail {
  overflow: hidden; }

.view {
  overflow: hidden;
  position: relative;
  text-align: center;
  box-shadow: 1px 1px 2px #e6e6e6;
  cursor: default; }

.view .mask, .view .content {
  position: absolute;
  width: 100%;
  overflow: hidden;
  top: 0;
  left: 0; }

.view img {
  display: block;
  position: relative; }

.view .tools {
  text-transform: uppercase;
  color: #fff;
  text-align: center;
  position: relative;
  font-size: 17px;
  padding: 20px;
  background: rgba(0, 0, 0, 0.35);
  margin: 60px 0 0 0; }

.mask.no-caption .tools {
  margin: 90px 0 0 0; }

.view .tools a {
  display: inline-block;
  color: #FFF;
  font-size: 18px;
  font-weight: 400;
  padding: 0 4px; }

.view p {
  font-family: Georgia, serif;
  font-style: italic;
  font-size: 12px;
  position: relative;
  color: #fff;
  padding: 10px 20px 20px;
  text-align: center; }

.view a.info {
  display: inline-block;
  text-decoration: none;
  padding: 7px 14px;
  background: #000;
  color: #fff;
  text-transform: uppercase;
  box-shadow: 0 0 1px #000; }

.view-first img {
  transition: all 0.2s linear; }

.view-first .mask {
  opacity: 0;
  background-color: rgba(0, 0, 0, 0.5);
  transition: all 0.4s ease-in-out; }

.view-first .tools {
  transform: translateY(-100px);
  opacity: 0;
  transition: all 0.2s ease-in-out; }

.view-first p {
  transform: translateY(100px);
  opacity: 0;
  transition: all 0.2s linear; }

.view-first:hover img {
  transform: scale(1.1); }

.view-first:hover .mask {
  opacity: 1; }

.view-first:hover .tools, .view-first:hover p {
  opacity: 1;
  transform: translateY(0px); }

.view-first:hover p {
  transition-delay: 0.1s; }

/* *********  /media gallery  **************************** */
</style>

@section('content_header')
@stop

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
    <i class="fas fa-box-open"></i> Galería de fotos Equipo <b>{{ $product->name }}</h3>
  </div>
  <div class="card-body">
    <form action="" id="form" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
    <input type="hidden" name="hdd_product_id" value="{{ $product->id }}" />
      <div class="row">    
            @if(session()->get('role')=='ADM')
              <div class="form-group col-sm-5">
                  <label>Foto </label><small> (Sólo formatos jpg, png. Máx. 2Mb.)</small>
                  <input id="photo" name="photo" type="file" required>
              </div>
              <div class="form-group col-sm-4">
                  <label>Título *</label><small> Max. 100 caracteres.</small>
                  {!! Form::text('title', null, ['id'=>'title', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
              </div>
              <div class="col-sm-3" style="margin-top: 7mm">
                <span style="display: inline;"> 
                  <button type="button" id="btn_add" class="btn btn-primary"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Subir</button>
                  <a href="{{URL::previous()}}" class="btn btn-default"><i class="fa fa-hand-o-left"></i> Regresar</a>
                </span>
              </div>
            @else
              <div class="form-group col-sm-12" align="right">
                  <a href="{{URL::previous()}}" class="btn btn-default"><i class="fa fa-hand-o-left"></i> Regresar</a>
              </div>
            @endif
            <div class="form-group col-sm-12">
              <span id="photos"></span>
            </div>              
      </div>
    </form>
  </div>
</div>

<!-- Modal para editar -->
<div class="modal inmodal" id="modalEdit" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog">
    <form action="#" id="form_update" method="POST">
    <input type="hidden" name="hdd_photo_id" id="hdd_photo_id" value=""/>
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-picture-o"></i> <strong>Editar Foto</strong></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>      
      <div class="modal-body">
        <div class="row">
          <div class="form-group col-sm-12">
            <label>Título *</label><small> Max. 100 caracteres.</small>
            {!! Form::text('new_title', null, ['id'=>'new_title', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
          </div>
          <div class="form-group col-sm-12" id="div_chk_main">
              <div class="icheck-primary d-inline">
                  {!! Form::checkbox('chk_main', null, false, ['id'=>'chk_main']) !!}
                  <label for="chk_main">Principal</label><small> Click para hacerla la foto principal de la galería.</small>
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btn_update" class="btn btn-sm btn-primary">Guardar</button>
      </div>
    </div>
    </form>
  </div>
</div>
<!-- /Modal para editar-->

@stop

@section('footer', 'Copyright © 2021. All rights reserved.' )

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
  
// Fileinput    
$('#photo').fileinput({
  language: 'es',
  allowedFileExtensions : ['jpg', 'jpeg', 'png'],
  showUpload: false,        
  maxFileSize: 2000,
  maxFilesNum: 1,
  overwriteInitial: true,
  showPreview: false,
  progressClass: true,
  progressCompleteClass: true,
}); 
$('.kv-upload-progress').hide();            


$("#btn_add").on('click', function(event) {
  var validator = $("#form" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    //Uso de FormData para envio de archivos via Ajax
    $(this).attr('disabled', true);
    var product_id='{{ $product->id }}';
    var form_data = new FormData($("#form")[0]);
    $.ajax({
      url: `{{URL::to("product_photo")}}`,
      type: 'POST',
      cache:true,
      processData: false,
      contentType: false,      
      data: form_data
    })
    .done(function(response) {
      $("#btn_add").attr('disabled', false);
      load_photos(product_id);      
      $('#photo').fileinput('reset');
      $('#title').val('');
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
    })
    .fail(function() {
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
    });
  }
});
  
function remove_photo(id){
  var product_id='{{ $product->id }}';
  $.ajax({
      url: `{{URL::to("product_photo")}}/${id}`,
      type: 'DELETE',
      cache:true,
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      load_photos(product_id);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  
  
function showModalEdit(photo_id, title, main){
  (main)?$("#div_chk_main").hide():$("#div_chk_main").show();
  $('#hdd_photo_id').val(photo_id);
  $('#new_title').val(title);
  $('#chk_main').prop('checked', false); 
  $("#modalEdit").modal("show");

};

$("#btn_update").on('click', function(event) {
  var validator = $("#form_update" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    var id=$('#hdd_photo_id').val();
    var product_id='{{ $product->id }}';
    $.ajax({
      url: `{{URL::to("product_photo")}}/${id}`,
      type: 'PUT',
      cache:true,
      data: {
        _token: "{{ csrf_token() }}", 
        title:$('#new_title').val(),
        main: $('#chk_main').is(":checked")?1:0
      },
    })
    .done(function(response) {
      $("#modalEdit").modal("toggle");
      load_photos(product_id); 
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
    })
    .fail(function() {
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
    });
  }
});

function load_photos(product_id){
  url = '{{URL::to("product_photo.load")}}/'+product_id;
  $('#photos').load(url);  
}

$(document).ready(function(){
  var product_id='{{ $product->id }}';
  load_photos(product_id);
});

</script>
@stop