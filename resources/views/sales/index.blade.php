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
    <i class="fas fa-shopping-cart" aria-hidden="true"></i> {{ ($type=='C')?'Cotizaciones':'Facturas' }}</h3>
  </div>
  <div class="card-body">
    <div class="col-sm-12 col-xs-12 text-right mb-2">
        <a href="{{ route('sales.load', [Crypt::encrypt(0), $type]) }}" class="btn btn-sm btn-primary" ><i class="fa fa-plus-circle"></i> Nueva {{ ($type=='C')?'Cotización':'Factura' }}</a>
        <a href="{{ route('sales.settings') }}" class="btn btn-sm btn-outline-primary" title="Configurar"><i class="fas fa-cogs"></i></a>
        <a href="{{ url('sales.rpt_sales', $type) }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
        <a href="{{ url('sales.xls_sales', $type) }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>        
    </div>
    <div class="table-responsive col-sm-12">  
      <table class="table table-striped table-hover" id="sales-table">
        <thead>
          <tr>
            <th text-align="center" width="5%"></th>
            <th>Folio</th>
            <th>Fecha</th>
            <th>Vencimiento</th>
            <th>Cliente</th>
            <th>Prospecto</th>
            <th>Monto</th>
            <th>PDF</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th></th>
            <th>Folio</th>
            <th>Fecha</th>
            <th>Vencimiento</th>
            <th>Cliente</th>
            <th>Prospecto</th>
            <th>Monto</th>
            <th>PDF</th>
          </tr>
        </tfoot>
      </table>
      <br><br>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-delete-sale">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar {{ ($type=='C')?'Cotización':'Factura' }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_sale_id" value=""/>
        <p>Esta seguro que desea eliminar la {{ ($type=='C')?'cotización':'factura' }} <b><span id="sale_number"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btn_delete_sale" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-send-sale">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="send_modal"></div>
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
  
function showModalDelete(sale_id, number){
  $('#hdd_sale_id').val(sale_id);
  $('#sale_number').html(number);
  $("#modal-delete-sale").modal("show");    
};
    
$("#btn_delete_sale").on('click', function(event) {    
    sale_delete($('#hdd_sale_id').val());
});

function sale_delete(id){  
  $.ajax({
      url: `{{URL::to("sales")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-sale').modal('toggle');
      $('#sales-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-sale').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function showModalSend(sale_id){
  url = `{{URL::to("sales.load_send_modal/")}}/${sale_id}`;
  $('#send_modal').load(url);  
  $("#modal-send-sale").modal("show");
}

function send_email(id, to){
    $.ajax({
        url: '{{URL::to("sales.send_email")}}/'+id+'/'+to,
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}", 
        },
    })
    .done(function(response) {
      $('#btn_send_sale').attr('disabled', false);
      $('#modal-send-sale').modal('toggle');
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
    })
    .fail(function(response) {
      toastr_msg('error', '{{ config('app.name') }}', 'Ocurrió un error enviando la cotización por correo', 4000);
    });
};

$("#customer_filter").select2({
  language: "es",
  placeholder: "Cliente - Todos",
  minimumResultsForSearch: 10,
  allowClear: true,
  width: '100%'
});

$("#customer_filter").change( event => {
  $('#sales-table').DataTable().draw();
});

$('#customer_filter').on("select2:unselect", function(event){
  $('#sales-table').DataTable().draw()  
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('vendor/datatables/js/es_ES.txt')}}";          
    var table=$('#sales-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('sales.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.type_filter= "{{ $type }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'folio',   name: 'folio', orderable: false, searchable: false},
            { data: 'date',   name: 'date', orderable: true, searchable: false},
            { data: 'due_date',   name: 'due_date', orderable: false, searchable: false, visible: ('{{ $type }}'=='C')?true:false},
            { data: 'customer',   name: 'customers.company_name', orderable: false, searchable: false},
            { data: 'prospect',   name: 'prospect', orderable: false, searchable: true},
            { data: 'total',   name: 'total', orderable: false, searchable: false},
            { data: 'pdf',   name: 'pdf', orderable: false, searchable: false}
        ]
    });
});
</script>
@stop