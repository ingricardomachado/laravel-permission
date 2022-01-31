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
    <i class="fas fa-store" aria-hidden="true"></i> {{ ($type=='O')?'Ordenes de compra':'Compras' }}</h3>
  </div>
  <div class="card-body">
    <div class="col-sm-12 col-xs-12 text-right mb-2">
        <a href="{{ route('purchases.load', [Crypt::encrypt(0), $type]) }}" class="btn btn-sm btn-primary" ><i class="fa fa-plus-circle"></i> Nueva {{ ($type=='O')?'Orden':'Compra' }}</a>
        <a href="{{ route('purchases.settings') }}" class="btn btn-sm btn-outline-primary" title="Configurar"><i class="fas fa-cogs"></i></a>
    </div>
    <div class="table-responsive col-sm-12">  
      <table class="table table-striped table-hover" id="purchases-table">
        <thead>
          <tr>
            <th text-align="center" width="5%"></th>
            <th>Folio</th>
            <th>Fecha</th>
            <th>Vencimiento</th>
            <th>Proveedor</th>
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
            <th>Proveedor</th>
            <th>Monto</th>
            <th>PDF</th>
          </tr>
        </tfoot>
      </table>
      <br><br>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-delete-purchase">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar {{ ($type=='O')?'Orden':'Compra' }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_purchase_id" value=""/>
        <p>Esta seguro que desea eliminar la {{ ($type=='O')?'orden de compra':'compra' }} <b><span id="purchase_number"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btn_delete_purchase" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-send-purchase">
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
  
function showModalDelete(purchase_id, number){
  $('#hdd_purchase_id').val(purchase_id);
  $('#purchase_number').html(number);
  $("#modal-delete-purchase").modal("show");    
};
    
$("#btn_delete_purchase").on('click', function(event) {    
    purchase_delete($('#hdd_purchase_id').val());
});

function purchase_delete(id){  
  $.ajax({
      url: `{{URL::to("purchases")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-purchase').modal('toggle');
      $('#purchases-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-purchase').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function showModalSend(purchase_id){
  url = `{{URL::to("purchases.load_send_modal/")}}/${purchase_id}`;
  $('#send_modal').load(url);  
  $("#modal-send-purchase").modal("show");
}

function send_email(id, to){
    $.ajax({
        url: '{{URL::to("purchases.send_email")}}/'+id+'/'+to,
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}", 
        },
    })
    .done(function(response) {
      $('#btn_send_purchase').attr('disabled', false);
      $('#modal-send-purchase').modal('toggle');
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
    })
    .fail(function(response) {
      toastr_msg('error', '{{ config('app.name') }}', 'Ocurrió un error enviando la orden por correo', 4000);
    });
};

$("#supplier_filter").select2({
  language: "es",
  placeholder: "Proveedor - Todos",
  minimumResultsForSearch: 10,
  allowClear: true,
  width: '100%'
});

$("#supplier_filter").change( event => {
  $('#purchases-table').DataTable().draw();
});

$('#supplier_filter').on("select2:unselect", function(event){
  $('#purchases-table').DataTable().draw()  
});

$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('vendor/datatables/js/es_ES.txt')}}";          
    var table=$('#purchases-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('purchases.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.type_filter= "{{ $type }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'folio',   name: 'folio', orderable: false, searchable: false},
            { data: 'date',   name: 'date', orderable: false, searchable: false},
            { data: 'due_date',   name: 'due_date', orderable: false, searchable: false, visible: ('{{ $type }}'=='C')?false:true},
            { data: 'supplier',   name: 'suppliers.company_name', orderable: false, searchable: false},
            { data: 'total',   name: 'total', orderable: false, searchable: false},
            { data: 'pdf',   name: 'pdf', orderable: false, searchable: false}
        ]
    });
});
</script>
@stop