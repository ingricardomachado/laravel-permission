@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.ICheck', true)


@section('content_header')
@stop

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
    <i class="fas fa-tools"></i> Servicios</h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="form-group col-sm-6 col-xs-12">
          {{ Form::select('category_filter', $categories, null, ['id'=>'category_filter', 'class'=>'select2', 'tabindex'=>'-1', 'placeholder'=>''])}}
      </div>
      <div class="form-group col-sm-6 col-xs-12 text-right">
          <a href="#" class="btn btn-sm btn-primary" onclick="showModalService(0);"><i class="fa fa-plus-circle"></i> Nuevo Servicio</a>
          <a href="{{ url('services.rpt_services') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
          <a href="{{ url('services.xls_services') }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>          
      </div>
      <div class="table-responsive col-sm-12">  
        <table class="table table-striped table-hover" id="services-table">
          <thead>
            <tr>
              <th text-align="center" width="5%"></th>
              <th width="10%">Código</th>
              <th width="30%">Nombre</th>
              <th width="10%">Estado</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th></th>
              <th>Código</th>
              <th>Nombre</th>
              <th>Estado</th>
            </tr>
          </tfoot>
        </table>
        <br><br>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Datos -->
<div class="modal fade" id="modal-service">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="service"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-service">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Servicio</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_service_id" value=""/>
        <p>Esta seguro que desea eliminar el servicio <b><span id="service_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_service" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>

@stop

@section('footer', 'Copyright © 2021. All rights reserved.' )

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
  
function showModalService(id){
  url = '{{URL::to("services.load")}}/'+id;
  $('#service').load(url);  
  $("#modal-service").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("services.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#services-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  

function showModalDelete(service_id, name){
  $('#hdd_service_id').val(service_id);
  $('#service_name').html(name);
  $("#modal-delete-service").modal("show");    
};
    
$("#btn_delete_service").on('click', function(event) {    
  service_delete($('#hdd_service_id').val());
});

function service_delete(id){  
  $.ajax({
      url: `{{URL::to("services")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-service').modal('toggle');
      $('#services-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-service').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function service_CRUD(id){
        
    var validator = $("#form_service").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_service")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("services")}}':'{{URL::to("services")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-service').modal('toggle');
          $('#services-table').DataTable().draw(); 
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_submit').attr('disabled', false);
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

$("#category_filter").change( event => {
  $('#services-table').DataTable().draw();
});

$(document).ready(function(){
                      
    $("#category_filter").select2({
        language: "es",
        placeholder: "Categoría - Todas",
        minimumResultsForSearch: 10,
        allowClear: true,
        width: '100%'
    });
    
    path_str_language = "{{URL::asset('vendor/datatables/js/es_ES.txt')}}";          
    var table=$('#services-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[2, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('services.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.category_filter = $('#category_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'number', orderable: false, searchable: true},
            { data: 'name',   name: 'name', orderable: true, searchable: true},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop