@extends('adminlte::page')

@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('plugins.ICheck', true)
@section('plugins.KartikFileinput', true)


@section('content_header')
@stop

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
    <i class="fas fa-truck" aria-hidden="true"></i> Provedores</h3>
  </div>
  <div class="card-body">
    <div class="form-group col-sm-12 col-xs-12 text-right">
        <a href="#" class="btn btn-sm btn-primary" onclick="showModalSupplier(0);"><i class="fa fa-plus-circle"></i> Nuevo Proveedor</a>
      <a href="{{ url('suppliers.rpt_suppliers') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
      <a href="{{ url('suppliers.xls_suppliers') }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>      
    </div>
    <div class="table-responsive col-sm-12">  
      <table class="table table-striped table-hover" id="suppliers-table">
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
          <input type="hidden" name="hdd_doc_supplier_id" id="hdd_doc_supplier_id" class="form-control" value="">
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
<div class="modal fade" id="modal-supplier">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="supplier"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-supplier">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Proveedor</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_supplier_id" value=""/>
        <p>Esta seguro que desea eliminar el proveedor <b><span id="supplier_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_supplier" class="btn btn-danger">Eliminar</button>
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
  
function showModalSupplier(id){
  url = '{{URL::to("suppliers.load")}}/'+id;
  $('#supplier').load(url);  
  $("#modal-supplier").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("suppliers.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#suppliers-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  
  
function showModalDelete(supplier_id, name){
  $('#hdd_supplier_id').val(supplier_id);
  $('#supplier_name').html(name);
  $("#modal-delete-supplier").modal("show");    
};
    
$("#btn_delete_supplier").on('click', function(event) {    
  supplier_delete($('#hdd_supplier_id').val());
});

function supplier_delete(id){  
  $.ajax({
      url: `{{URL::to("suppliers")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-supplier').modal('toggle');
      $('#suppliers-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-supplier').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function supplier_CRUD(id){
        
    var validator = $("#form_supplier").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit_supplier').attr('disabled', true);
        var form_data = new FormData($("#form_documents")[0]);
        array_names.forEach((item) => form_data.append("names[]", item));
        array_occupations.forEach((item) => form_data.append("occupations[]", item));
        array_positions.forEach((item) => form_data.append("positions[]", item));
        array_phones.forEach((item) => form_data.append("phones[]", item));
        array_emails.forEach((item) => form_data.append("emails[]", item));
        array_mains.forEach((item) => form_data.append("mains[]", item));
        form_data.append("name", $('#name').val());
        form_data.append("target", $('#target').val());
        form_data.append("phone", $('#phone').val());
        form_data.append("email", $('#email').val());
        form_data.append("country", $('#country').val());
        form_data.append("state", $('#state').val());
        form_data.append("address", $('#address').val());
        form_data.append("city", $('#city').val());
        form_data.append("zipcode", $('#zipcode').val());
        form_data.append("location", $('#location').val());
        form_data.append("urls", $('#urls').val());
        form_data.append("bank_accounts", $('#bank_accounts').val());
        form_data.append("bussines_name", $('#bussines_name').val());
        form_data.append("rfc", $('#rfc').val());
        form_data.append("bussines_address", $('#bussines_address').val());
        form_data.append("notes", $('#notes').val());
        $.ajax({
          url:(id==0)?'{{URL::to("suppliers")}}':'{{URL::to("suppliers")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit_supplier').attr('disabled', false);
          $('#modal-supplier').modal('toggle');
          $('#suppliers-table').DataTable().draw(); 
          toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
        })
        .fail(function(response) {
          if(response.status == 422){
            $('#btn_submit_supplier').attr('disabled', false);
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


//DOCUMENTS
$('#file').fileinput({
    language: 'es',
    allowedFileExtensions : ['pdf', 'xls', 'xlsx', 'doc', 'docx', 'odt', 'ods'],
    previewFileIcon: '<i class="fas fa-exclamation-triangle"></i>',
    browseLabel: '<i class="fas fa-folder-open"></i>',
    removeLabel: '<i class="fas fa-trash"></i>',
    showUpload: false,
    showCancel: false,        
    maxFileSize: 10000,
    maxFilesNum: 1,
    overwriteInitial: true,
    progressClass: true,
    progressCompleteClass: true,
    showPreview: false,
});
$('.kv-upload-progress').hide();            

function showModalDocument(supplier_id){
  $('#hdd_doc_supplier_id').val(supplier_id, name);
  $('#file').fileinput('reset');  
  $("#modal-document").modal("show");
}

function showModalDeleteDocument(id, name){
  $('#hdd_document_id').val(id);
  $('#span_document_name').html(name);
  $("#modal-delete-document").modal("show");    
};

$("#btn_delete_document").on('click', function(event) {    
    document_delete($('#hdd_document_id').val());
});

function upload_document(){
  var validator = $("#form_document").validate();
  formulario_validado = validator.form();
  if(formulario_validado){
      $('#btn_document').attr('disabled', true);
      var form_data = new FormData($("#form_document")[0]);
      form_data.append('supplier_id', $('#hdd_doc_supplier_id').val());
      $.ajax({
        url:'{{URL::to("supplier_documents")}}',
        type:'POST',
        cache:true,
        processData: false,
        contentType: false,      
        data: form_data
      })
      .done(function(response) {
        $('#btn_document').attr('disabled', false);
        $('#modal-document').modal('toggle');
        $('#suppliers-table').DataTable().draw(false); 
        toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
      })
      .fail(function(response) {
        if(response.visivility == 422){
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

function document_delete(id){  
  $.ajax({
      url: `{{URL::to("supplier_documents")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-document').modal('toggle');
      $('#suppliers-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
      $('#modal-delete-document').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

//CONTACTS
function showModalContact(supplier_id, id){
  url = '{{URL::to("supplier_contacts.load")}}/'+supplier_id+'/'+id;
  $('#contact').load(url);  
  $("#modal-contact").modal("show");
}


$(document).ready(function(){
                      
    path_str_language = "{{URL::asset('vendor/datatables/js/es_ES.txt')}}";          
    var table=$('#suppliers-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[2, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('suppliers.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'suppliers.number', orderable: true, searchable: true},
            { data: 'name',   name: 'suppliers.name', orderable: true, searchable: true},
            { data: 'contacts',   name: 'contacts', orderable: false, searchable: false},
            { data: 'phone',   name: 'phone', orderable: false, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop