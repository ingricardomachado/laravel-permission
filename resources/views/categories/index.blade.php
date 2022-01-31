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
    <i class="fas fa-th-large"></i> Categorías</h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="form-group col-sm-4 col-xs-12">
      </div>
      <div class="col-sm-8 col-xs-12 text-right">
          <a href="#" class="btn btn-sm btn-primary" onclick="showModalCategory(0);"><i class="fa fa-plus-circle"></i> Nueva Categoría</a>
        <a href="{{ url('categories.rpt_categories') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a><br><br>
      </div>
      <div class="table-responsive col-sm-12">  
        <table class="table table-striped table-hover" id="categories-table">
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
<div class="modal fade" id="modal-category">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="category"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-category">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Categoría</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_category_id" value=""/>
        <p>Esta seguro que desea eliminar la categoría <b><span id="category_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_category" class="btn btn-danger">Eliminar</button>
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
  
function showModalCategory(id){
  url = '{{URL::to("categories.load")}}/'+id;
  $('#category').load(url);  
  $("#modal-category").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("categories.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#categories-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  

function showModalDelete(category_id, name){
  $('#hdd_category_id').val(category_id);
  $('#category_name').html(name);
  $("#modal-delete-category").modal("show");    
};
    
$("#btn_delete_category").on('click', function(event) {    
  category_delete($('#hdd_category_id').val());
});

function category_delete(id){  
  $.ajax({
      url: `{{URL::to("categories")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-category').modal('toggle');
      $('#categories-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-category').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function category_CRUD(id){
        
    var validator = $("#form_category").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_category")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("categories")}}':'{{URL::to("categories")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-category').modal('toggle');
          $('#categories-table').DataTable().draw(); 
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

$("#type_filter").change( event => {
  $('#categories-table').DataTable().draw();
});

$(document).ready(function(){
                      
  $("#type_filter").select2({
      language: "es",
      placeholder: "Tipo - Todas",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
  });
    
    path_str_language = "{{URL::asset('vendor/datatables/js/es_ES.txt')}}";          
    var table=$('#categories-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('categories.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.type_filter = $('#type_filter').val();
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