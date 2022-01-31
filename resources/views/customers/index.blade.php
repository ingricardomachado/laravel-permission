@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.ICheck', true)
@section('plugins.KartikFileinput', true)
@section('plugins.InternationalTelephoneInput', true)

@section('content_header')
@stop

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
    <i class="fa fa-users" aria-hidden="true"></i> Clientes</h3>
  </div>
  <div class="card-body">
    <div class="form-group col-sm-12 col-xs-12 text-right">
        <a href="#" class="btn btn-sm btn-primary" onclick="showModalCustomer(0);"><i class="fa fa-plus-circle"></i> Nuevo Cliente</a>
      <a href="{{ url('customers.rpt_customers') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
      <a href="{{ url('customers.xls_customers') }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>      
    </div>
    <div class="table-responsive col-sm-12">  
      <table class="table table-striped table-hover" id="customers-table">
        <thead>
          <tr>
            <th text-align="center" width="5%"></th>
            <th width="5%">Código</th>
            <th width="20%">Empresa</th>
            <th width="20%">Contacto</th>
            <th width="10%">Teléfono</th>
            <th width="10%">Estado</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th></th>
            <th>Código</th>
            <th>Empresa</th>
            <th>Contactos</th>
            <th>Teléfono</th>
            <th>Estado</th>
          </tr>
        </tfoot>
      </table>
      <br><br>
    </div>
  </div>
</div>

<!-- Modal para Documento -->
<div class="modal fade" id="modal-document">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" id="form_document" method="POST">
          <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
          <input type="hidden" name="hdd_doc_customer_id" id="hdd_doc_customer_id" class="form-control" value="">
          <div class="modal-header">
            <h5 class="modal-title"><i class="far fa-file-alt"></i> Subir Documento <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
              <div class="row">                            
                  <div class="col-sm-12">
                    <div class="form-group">
                        <label>Archivo </label><small> (Sólo formatos pdf, xls, xlsx, doc, docs, odt, ods. Máx. 10Mb.)</small>
                        <input id="file" name="file" class="file" type="file" required>
                    </div>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" id="btn_document" onclick="upload_document()" class="btn btn-sm btn-primary">Subir</button>
              <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
          </div>
      </form>

    </div>
  </div>
</div>
<!-- /Modal para Documento -->

<!-- Modal para eliminar document -->
<div class="modal fade" id="modal-delete-document">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Documento</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_document_id" value=""/>
        <p>Esta seguro que desea eliminar el documento <b><span id="span_document_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_document" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal para eliminar document -->

<!-- Modal para Datos -->
<div class="modal fade" id="modal-customer">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="customer"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-customer">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Cliente</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_customer_id" value=""/>
        <p>Esta seguro que desea eliminar el cliente <b><span id="customer_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_customer" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Contacto -->
<div class="modal fade" id="modal-contact">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="contact"></div>
    </div>
  </div>
</div>
<!-- /Modal para Contacto -->

@stop

@section('footer', 'Copyright © 2021. All rights reserved.' )

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
  
function showModalCustomer(id){
  url = '{{URL::to("customers.load")}}/'+id;
  $('#customer').load(url);  
  $("#modal-customer").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("customers.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#customers-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  
  
function showModalRevoke(customer_id, name){
  $('#hdd_customer_revoke_id').val(customer_id);
  $('#customer_revoke_name').html(name);
  $("#modalRevoke").modal("show");    
};

$("#btn_revoke_customer").on('click', function(event) {    
    revoke($('#hdd_customer_revoke_id').val());
});

function revoke(id){
  $.ajax({
    url: `{{URL::to("customers.revoke")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#customers-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  


function showModalDelete(customer_id, name){
  $('#hdd_customer_id').val(customer_id);
  $('#customer_name').html(name);
  $("#modal-delete-customer").modal("show");    
};
    
$("#btn_delete_customer").on('click', function(event) {    
  customer_delete($('#hdd_customer_id').val());
});

function customer_delete(id){  
  $.ajax({
      url: `{{URL::to("customers")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-customer').modal('toggle');
      $('#customers-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-customer').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function customer_CRUD(id){
    var validator = $("#form_customer").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit_customer').attr('disabled', true);
        var form_data = new FormData($("#form_documents")[0]);
        array_names.forEach((item) => form_data.append("names[]", item));
        array_occupations.forEach((item) => form_data.append("occupations[]", item));
        array_positions.forEach((item) => form_data.append("positions[]", item));
        array_phones.forEach((item) => form_data.append("phones[]", item));
        array_emails.forEach((item) => form_data.append("emails[]", item));
        array_mains.forEach((item) => form_data.append("mains[]", item));
        form_data.append("sucursal", ($('#sucursal').is(':checked'))?1:0);
        form_data.append("change_parent", ($('#change_parent').is(':checked'))?1:0);
        form_data.append("create_user", ($('#create_user').is(':checked'))?1:0);
        form_data.append("change_password", ($('#change_password').is(':checked'))?1:0);
        form_data.append("parent", $('#parent').val());
        form_data.append("name", $('#name').val());
        form_data.append("target", $('#target').val());
        form_data.append("phone", $('#phone').val());
        form_data.append("email", $('#email').val());
        form_data.append("street", $('#street').val());
        form_data.append("street_number", $('#street_number').val());
        form_data.append("neighborhood", $('#neighborhood').val());
        form_data.append("zipcode", $('#zipcode').val());
        form_data.append("city", $('#city').val());
        form_data.append("country", $('#country').val());
        form_data.append("state", $('#state').val());
        form_data.append("address", $('#address').val());
        form_data.append("notes", $('#notes').val());
        form_data.append("urls", $('#urls').val());
        form_data.append("bussines_name", $('#bussines_name').val());
        form_data.append("rfc", $('#rfc').val());
        form_data.append("bussines_address", $('#bussines_address').val());
        form_data.append("shipping_street", $('#shipping_street').val());
        form_data.append("shipping_address", $('#shipping_address').val());
        form_data.append("shipping_number", $('#shipping_number').val());
        form_data.append("shipping_neighborhood", $('#shipping_neighborhood').val());
        form_data.append("shipping_zipcode", $('#shipping_zipcode').val());
        form_data.append("shipping_country", $('#shipping_country').val());
        form_data.append("shipping_state", $('#shipping_state').val());
        form_data.append("shipping_city", $('#shipping_city').val());
        form_data.append("discount", $('#discount').val());
        $.ajax({
          url:(id==0)?'{{URL::to("customers")}}':'{{URL::to("customers")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit_customer').attr('disabled', false);
          $('#modal-customer').modal('toggle');
          $('#customers-table').DataTable().draw(); 
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_submit_customer').attr('disabled', false);
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
    var table=$('#customers-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[2, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('customers.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'customers.number', orderable: true, searchable: true},
            { data: 'name',   name: 'customers.name', orderable: true, searchable: true},
            { data: 'contacts',   name: 'contacts', orderable: false, searchable: false},
            { data: 'phone',   name: 'phone', orderable: false, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop