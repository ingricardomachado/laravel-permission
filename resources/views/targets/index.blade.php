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
    <i class="fas fa-list"></i> Giros</h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="form-group col-sm-4 col-xs-12">
      </div>
      <div class="col-sm-8 col-xs-12 text-right">
          <a href="#" class="btn btn-sm btn-primary" onclick="showModalTarget(0);"><i class="fa fa-plus-circle"></i> Nuevo Giro</a>
        <a href="{{ url('targets.rpt_targets') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a><br><br>
      </div>
      <div class="table-responsive col-sm-12">  
        <table class="table table-striped table-hover" id="targets-table">
          <thead>
            <tr>
              <th text-align="center" width="5%"></th>
              <th width="85%">Nombre</th>
              <th width="10%">Estado</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th></th>
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
<div class="modal fade" id="modal-target">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="target"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-target">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Giro</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_target_id" value=""/>
        <p>Esta seguro que desea eliminar el giro <b><span id="target_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_target" class="btn btn-danger">Eliminar</button>
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
  
function showModalTarget(id){
  url = '{{URL::to("targets.load")}}/'+id;
  $('#target').load(url);  
  $("#modal-target").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("targets.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#targets-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  

function showModalDelete(target_id, name){
  $('#hdd_target_id').val(target_id);
  $('#target_name').html(name);
  $("#modal-delete-target").modal("show");    
};
    
$("#btn_delete_target").on('click', function(event) {    
  target_delete($('#hdd_target_id').val());
});

function target_delete(id){  
  $.ajax({
      url: `{{URL::to("targets")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-target').modal('toggle');
      $('#targets-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-target').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function target_CRUD(id){
        
    var validator = $("#form_target").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_target")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("targets")}}':'{{URL::to("targets")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-target').modal('toggle');
          $('#targets-table').DataTable().draw(); 
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
  var table=$('#targets-table').DataTable({
      "oLanguage":{"sUrl":path_str_language},
      "aaSorting": [[1, "asc"]],
      processing: true,
      serverSide: true,
      ajax: {
          url: '{!! route('targets.datatable') !!}',
          type: "POST",
          data: function(d) {
              d._token= "{{ csrf_token() }}";
          }
      },        
      columns: [
          { data: 'action', name: 'action', orderable: false, searchable: false},
          { data: 'name',   name: 'name', orderable: false, searchable: true},
          { data: 'status', name: 'status', orderable: false, searchable: false }
      ]
  });

});
</script>
@stop