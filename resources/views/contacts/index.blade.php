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
    <i class="fas fa-users" aria-hidden="true"></i> Contactos</h3>
  </div>
  <div class="card-body">
    <div class="form-group col-sm-12 col-xs-12 text-right">
        <a href="#" class="btn btn-sm btn-primary" onclick="showModalContact(0);"><i class="fa fa-plus-circle"></i> Nuevo Contacto</a>
      <a href="{{ url('contacts.rpt_contacts') }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a>
      <a href="{{ url('contacts.xls_contacts') }}" class="btn btn-sm btn-default" target="_blank" title="Exportar Excel">XLS</a>      
    </div>
    <div class="table-responsive col-sm-12">  
      <table class="table table-striped table-hover" id="contacts-table">
        <thead>
          <tr>
            <th text-align="center" width="5%"></th>
            <th width="5%">Código</th>
            <th width="20%">Nombre</th>
            <th width="10%">Celular</th>
            <th width="10%">Teléfono</th>
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
            <th>Estado</th>
          </tr>
        </tfoot>
      </table>
      <br><br>
    </div>
  </div>
</div>

<!-- Modal para Datos -->
<div class="modal fade" id="modal-contact">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="contact"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<div class="modal fade" id="modal-delete-contact">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Contacto</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_contact_id" value=""/>
        <p>Esta seguro que desea eliminar el proveedor <b><span id="contact_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_contact" class="btn btn-danger">Eliminar</button>
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
  
function showModalContact(id){
  url = '{{URL::to("contacts.load")}}/'+id;
  $('#contact').load(url);  
  $("#modal-contact").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("contacts.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#contacts-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  
  
function showModalDelete(contact_id, name){
  $('#hdd_contact_id').val(contact_id);
  $('#contact_name').html(name);
  $("#modal-delete-contact").modal("show");    
};
    
$("#btn_delete_contact").on('click', function(event) {    
  contact_delete($('#hdd_contact_id').val());
});

function contact_delete(id){  
  $.ajax({
      url: `{{URL::to("contacts")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-contact').modal('toggle');
      $('#contacts-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-contact').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function contact_CRUD(id){
        
    var validator = $("#form_contact").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_contact")[0]);
        form_data.append('full_name', $('#first_name').val()+' '+$('#last_name').val());
        $.ajax({
          url:(id==0)?'{{URL::to("contacts")}}':'{{URL::to("contacts")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-contact').modal('toggle');
          $('#contacts-table').DataTable().draw(); 
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
    var table=$('#contacts-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[2, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('contacts.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'contacts.number', orderable: true, searchable: true},
            { data: 'name',   name: 'contacts.full_name', orderable: true, searchable: true},
            { data: 'cell',   name: 'cell', orderable: false, searchable: false},
            { data: 'phone',   name: 'phone', orderable: false, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop