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
    <i class="fa fa-users" aria-hidden="true"></i> Empleados</h3>
  </div>
  <div class="card-body">
    <div class="form-group col-sm-12 col-xs-12 text-right">
        <a href="#" class="btn btn-sm btn-primary" onclick="showModalEmployee(0);"><i class="fa fa-plus-circle"></i> Nuevo Empleado</a>
      <a href="{{ url('employees.rpt_employees') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
      <a href="{{ url('employees.xls_employees') }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>      
    </div>
    <div class="table-responsive col-sm-12">  
      <table class="table table-striped table-hover" id="employees-table">
        <thead>
          <tr>
            <th text-align="center" width="5%"></th>
            <th width="5%">Código</th>
            <th width="20%">Nombre</th>
            <th width="10%">Celular</th>
            <th width="10%">Teléfono</th>
            <th width="10%">Rol</th>
            <th width="10%">Estado</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th></th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Celular</th>
            <th>Teléfono</th>
            <th>Rol</th>
            <th>Estado</th>
          </tr>
        </tfoot>
      </table>
      <br><br>
    </div>
  </div>
</div>

<!-- Modal para Datos -->
<div class="modal fade" id="modal-employee">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="employee"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-employee">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Empleado</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_employee_id" value=""/>
        <p>Esta seguro que desea eliminar el empleado <b><span id="employee_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_employee" class="btn btn-danger">Eliminar</button>
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
  
function showModalEmployee(id){
  url = '{{URL::to("employees.load")}}/'+id;
  $('#employee').load(url);  
  $("#modal-employee").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("employees.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#employees-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  
  
function showModalRevoke(employee_id, name){
  $('#hdd_employee_revoke_id').val(employee_id);
  $('#employee_revoke_name').html(name);
  $("#modalRevoke").modal("show");    
};

$("#btn_revoke_employee").on('click', function(event) {    
    revoke($('#hdd_employee_revoke_id').val());
});

function revoke(id){
  $.ajax({
    url: `{{URL::to("employees.revoke")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#employees-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  


function showModalDelete(employee_id, name){
  $('#hdd_employee_id').val(employee_id);
  $('#employee_name').html(name);
  $("#modal-delete-employee").modal("show");    
};
    
$("#btn_delete_employee").on('click', function(event) {    
  employee_delete($('#hdd_employee_id').val());
});

function employee_delete(id){  
  $.ajax({
      url: `{{URL::to("employees")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-employee').modal('toggle');
      $('#employees-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-employee').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function employee_CRUD(id){
        
    var validator = $("#form_employee").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_employee")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("employees")}}':'{{URL::to("employees")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-employee').modal('toggle');
          $('#employees-table').DataTable().draw(); 
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
    var table=$('#employees-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[2, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('employees.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'employees.number', orderable: true, searchable: true},
            { data: 'full_name',   name: 'employees.full_name', orderable: true, searchable: true},
            { data: 'cell',   name: 'cell', orderable: false, searchable: false},
            { data: 'phone',   name: 'phone', orderable: false, searchable: false},
            { data: 'role',   name: 'role', orderable: false, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop