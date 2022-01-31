@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.ICheck', true)
@section('plugins.Moment', true)
@section('plugins.TempusDominus', true)

@section('content_header')
@stop

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
    <i class="fas fa-file-signature"></i> Ordenes de Servicio</h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="form-group col-sm-4 col-xs-12">
          {{ Form::select('customer_filter', $customers, null, ['id'=>'customer_filter', 'class'=>'select2', 'tabindex'=>'-1', 'placeholder'=>''])}}
      </div>
      <div class="form-group col-sm-8 col-xs-12 text-right">
        <a href="{{ url('service_orders.rpt_service_orders') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
        <a href="{{ url('service_orders.xls_service_orders') }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>        
      </div>
      <div class="table-responsive col-sm-12">  
        <table class="table table-striped table-hover" id="service_orders-table">
          <thead>
            <tr>
              <th text-align="center" width="5%"></th>
              <th width="15%">Folio</th>
              <th width="15%">Fecha</th>
              <th width="40%">Cliente</th>
              <th width="10%">PDF</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th></th>
              <th>Folio</th>
              <th>Fecha</th>
              <th>Cliente</th>
              <th>PDF</th>
            </tr>
          </tfoot>
        </table>
        <br><br>
      </div>
    </div>
  </div>
</div>


<!-- Modal para Datos -->
<div class="modal fade" id="modal-service_order">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="service_order"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-service_order">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Orden de Servicio</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_service_order_id" value=""/>
        <p>Esta seguro que desea eliminar la orden de servicio <b><span id="service_order_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_service_order" class="btn btn-danger">Eliminar</button>
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
  
function showModalDelete(service_order_id, name){
  $('#hdd_service_order_id').val(service_order_id);
  $('#service_order_name').html(name);
  $("#modal-delete-service_order").modal("show");    
};
    
$("#btn_delete_service_order").on('click', function(event) {    
  service_order_delete($('#hdd_service_order_id').val());
});

function service_order_delete(id){  
  $.ajax({
      url: `{{URL::to("service_orders")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-service_order').modal('toggle');
      $('#service_orders-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-service_order').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

$("#customer_filter").change( event => {
  $('#service_orders-table').DataTable().draw();
});

$(document).ready(function(){
                      
  $("#customer_filter").select2({
      language: "es",
      placeholder: "Cliente - Todos",
      minimumResultsForSearch: 10,
      allowClear: true,
      width: '100%'
  });
    
    path_str_language = "{{URL::asset('vendor/datatables/js/es_ES.txt')}}";          
    var table=$('#service_orders-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('service_orders.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.customer_filter = $('#customer_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'folio',   name: 'folio', orderable: false, searchable: false},
            { data: 'date',   name: 'date', orderable: false, searchable: false},
            { data: 'customer', name: 'customer', orderable: false, searchable: false },
            { data: 'pdf', name: 'pdf', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop