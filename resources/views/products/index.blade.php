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
    <i class="fas fa-box-open"></i> Productos</h3>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="form-group col-sm-6 col-xs-12">
          {{ Form::select('category_filter', $categories, null, ['id'=>'category_filter', 'class'=>'select2', 'tabindex'=>'-1', 'placeholder'=>''])}}
      </div>
      <div class="form-group col-sm-6 col-xs-12 text-right">
          <a href="#" class="btn btn-sm btn-primary" onclick="showModalProduct(0);"><i class="fa fa-plus-circle"></i> Nuevo Producto</a>
          <a href="{{ url('products.rpt_products') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
          <a href="{{ url('products.xls_products') }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>
      </div>
      <div class="table-responsive col-sm-12">  
        <table class="table table-striped table-hover" id="products-table">
          <thead>
            <tr>
              <th text-align="center" width="5%"></th>
              <th width="10%">Código</th>
              <th width="30%">Nombre</th>
              <th width="10%">Stock</th>
              <th width="10%">Unidad</th>
              <th width="20%">Proveedor</th>
              <th width="10%">Estado</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th></th>
              <th>Código</th>
              <th>Nombre</th>
              <th>Stock</th>
              <th>Unidad</th>
              <th>Proveedor</th>
              <th>Estado</th>
            </tr>
          </tfoot>
        </table>
        <br><br>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Documento -->
<div class="modal fade" id="modal-document">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="" id="form_document" method="POST">
          <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
          <input type="hidden" name="hdd_doc_product_id" id="hdd_doc_product_id" class="form-control" value="">
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
<!-- Modal para eliminar producto -->

<!-- Modal para Datos -->
<div class="modal fade" id="modal-product">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="product"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para eliminar -->
<div class="modal fade" id="modal-delete-product">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Producto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_product_id" value=""/>
        <p>Esta seguro que desea eliminar el producto <b><span id="product_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_product" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal para eliminar -->
@stop

@section('footer', 'Copyright © 2021. All rights reserved.' )

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
  
function showModalProduct(id){
  url = '{{URL::to("products.load")}}/'+id;
  $('#product').load(url);  
  $("#modal-product").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("products.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#products-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  

function showModalDelete(product_id, name){
  $('#hdd_product_id').val(product_id);
  $('#product_name').html(name);
  $("#modal-delete-product").modal("show");    
};
    
$("#btn_delete_product").on('click', function(event) {    
  product_delete($('#hdd_product_id').val());
});

function product_delete(id){  
  $.ajax({
      url: `{{URL::to("products")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-product').modal('toggle');
      $('#products-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-product').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

$("#category_filter").change( event => {
  $('#products-table').DataTable().draw();
});

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

function showModalDocument(product_id){
  $('#hdd_doc_product_id').val(product_id, name);
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
      form_data.append('product_id', $('#hdd_doc_product_id').val());
      $.ajax({
        url:'{{URL::to("product_documents")}}',
        type:'POST',
        cache:true,
        processData: false,
        contentType: false,      
        data: form_data
      })
      .done(function(response) {
        $('#btn_document').attr('disabled', false);
        $('#modal-document').modal('toggle');
        $('#products-table').DataTable().draw(false); 
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
      url: `{{URL::to("product_documents")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-document').modal('toggle');
      $('#products-table').DataTable().draw(false);
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
      $('#modal-delete-document').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

function product_CRUD(id){
        
    var validator = $("#form_product").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_product")[0]);
        form_data.append('spare', $('#chk_spare').is(":checked")?1:0);
        $.ajax({
          url:(id==0)?'{{URL::to("products")}}':'{{URL::to("products")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-product').modal('toggle');
          $('#products-table').DataTable().draw(); 
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
                      
    $("#category_filter").select2({
        language: "es",
        placeholder: "Categoría - Todas",
        minimumResultsForSearch: 10,
        allowClear: true,
        width: '100%'
    });
    
    path_str_language = "{{URL::asset('vendor/datatables/js/es_ES.txt')}}";          
    var table=$('#products-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[2, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('products.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.category_filter = $('#category_filter').val();
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'number', orderable: false, searchable: true},
            { data: 'name',   name: 'name', orderable: true, searchable: true},
            { data: 'stock',   name: 'stock', orderable: false, searchable: false},
            { data: 'unit',   name: 'unit', orderable: false, searchable: false},
            { data: 'supplier',   name: 'supplier', orderable: false, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop