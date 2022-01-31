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
    <i class="fa fa-users" aria-hidden="true"></i> Unidades</h3>
  </div>
  <div class="card-body">
    <div class="col-sm-12 col-xs-12 text-right">
        <a href="#" class="btn btn-sm btn-primary" onclick="showModalUnit(0);"><i class="fa fa-plus-circle"></i> Nueva Unidad</a>
      <a href="{{ url('units.rpt_units') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a><br><br>
    </div>
    <div class="table-responsive col-sm-12">  
      <table class="table table-striped table-hover" id="units-table">
        <thead>
          <tr>
            <th text-align="center" width="5%"></th>
            <th width="10%">Unidad</th>
            <th width="20%">Nombre</th>
            <th width="10%">Estado</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th></th>
            <th>Unidad</th>
            <th>Nombre</th>
            <th>Estado</th>
          </tr>
        </tfoot>
      </table>
      <br><br>
    </div>
  </div>
</div>

<!-- Modal para Datos -->
<div class="modal fade" id="modal-unit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="unit"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-unit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Unidad</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_unit_id" value=""/>
        <p>Esta seguro que desea eliminar la unidad <b><span id="unit_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_unit" class="btn btn-danger">Eliminar</button>
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
  
function showModalUnit(id){
  url = '{{URL::to("units.load")}}/'+id;
  $('#unit').load(url);  
  $("#modal-unit").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("units.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#units-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  

function showModalDelete(unit_id, name){
  $('#hdd_unit_id').val(unit_id);
  $('#unit_name').html(name);
  $("#modal-delete-unit").modal("show");    
};
    
$("#btn_delete_unit").on('click', function(event) {    
  unit_delete($('#hdd_unit_id').val());
});

function unit_delete(id){  
  $.ajax({
      url: `{{URL::to("units")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-unit').modal('toggle');
      $('#units-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-unit').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function unit_CRUD(id){
        
    var validator = $("#form_unit").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_unit")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("units")}}':'{{URL::to("units")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-unit').modal('toggle');
          $('#units-table').DataTable().draw(); 
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

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('vendor/datatables/js/es_ES.txt')}}";          
    var table=$('#units-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('units.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'unit',   name: 'unit', orderable: false, searchable: true},
            { data: 'name',   name: 'name', orderable: false, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop