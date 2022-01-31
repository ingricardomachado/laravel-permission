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
    <i class="far fa-money-bill-alt"></i> Cuentas por Cobrar</h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="form-group col-sm-4 col-xs-12">
          {{ Form::select('customer_filter', $customers, null, ['id'=>'customer_filter', 'class'=>'select2', 'tabindex'=>'-1', 'placeholder'=>''])}}
      </div>
      <div class="form-group col-sm-8 col-xs-12 text-right">
          <a href="#" class="btn btn-sm btn-primary" onclick="showModalReceivable(0);"><i class="fa fa-plus-circle"></i> Nueva Cuenta por Cobrar</a>
          <a href="{{ url('receivables.rpt_receivables') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
          <a href="{{ url('receivables.xls_receivables') }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>          
      </div>
      <div class="table-responsive col-sm-12">  
        <table class="table table-striped table-hover" id="receivables-table">
          <thead>
            <tr>
              <th text-align="center" width="5%"></th>
              <th width="5%">Código</th>
              <th width="10%">Fecha</th>
              <th width="30%">Cliente</th>
              <th width="10%">Monto</th>
              <th width="10%">Folio</th>
              <th width="10%">Forma pago</th>
              <th width="10%">Metodo pago</th>
              <th width="10%">Condición pago</th>
              <th width="10%">Plazo días</th>
              <th width="10%">Balance</th>
              <th width="10%">Fecha cierre</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
              <th></th>
            </tr>
          </tfoot>
        </table>
        <br><br>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Datos -->
<div class="modal fade" id="modal-receivable">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="receivable"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-receivable">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Cuenta por Cobrar</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_receivable_id" value=""/>
        <p>Esta seguro que desea eliminar la cuenta por cobrar <b><span id="receivable_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_receivable" class="btn btn-danger">Eliminar</button>
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
  
function showModalReceivable(id){
  url = '{{URL::to("receivables.load")}}/'+id;
  $('#receivable').load(url);  
  $("#modal-receivable").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("receivables.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#receivables-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  

function showModalDelete(receivable_id, name){
  $('#hdd_receivable_id').val(receivable_id);
  $('#receivable_name').html(name);
  $("#modal-delete-receivable").modal("show");    
};
    
$("#btn_delete_receivable").on('click', function(event) {    
  receivable_delete($('#hdd_receivable_id').val());
});

function receivable_delete(id){  
  $.ajax({
      url: `{{URL::to("receivables")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-receivable').modal('toggle');
      $('#receivables-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-receivable').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function receivable_CRUD(id){
        
    var validator = $("#form_receivable").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_receivable")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("receivables")}}':'{{URL::to("receivables")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-receivable').modal('toggle');
          $('#receivables-table').DataTable().draw(); 
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

$("#customer_filter").change( event => {
  $('#receivables-table').DataTable().draw();
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
    var table=$('#receivables-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('receivables.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.customer_filter = $('#customer_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'number', orderable: false, searchable: false},
            { data: 'date',   name: 'date', orderable: false, searchable: false},
            { data: 'customer',   name: 'customers.name', orderable: false, searchable: false},
            { data: 'amount',   name: 'amount', orderable: false, searchable: false},
            { data: 'folio',   name: 'folio', orderable: false, searchable: false},
            { data: 'way_pay',   name: 'way_pay', orderable: false, searchable: false},
            { data: 'method_pay',   name: 'method_pay', orderable: false, searchable: false},
            { data: 'condition_pay',   name: 'condition_pay', orderable: false, searchable: false},
            { data: 'days',   name: 'days', orderable: false, searchable: false},
            { data: 'balance',   name: 'balance', orderable: false, searchable: false},
            { data: 'close_date',   name: 'close_date', orderable: false, searchable: false}
        ]
    });
});
</script>
@stop