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
    <i class="fa fa-users" aria-hidden="true"></i> Suscriptores permanentes</h3>
  </div>
  <div class="card-body">
    <div class="col-sm-12 col-xs-12 text-right">
        <a href="#" class="btn btn-sm btn-primary" onclick="showModalSubscriber(0);"><i class="fa fa-plus-circle"></i> Nuevo Suscriptor</a>
      <a href="{{ url('subscribers.rpt_subscribers', 0) }}" class="btn btn-sm btn-default" target="_blank" title="Imprimir PDF"><i class="fa fa-print"></i></a><br><br>
    </div>
    <div class="table-responsive col-sm-12">  
      <table class="table table-striped" id="subscribers-table">
        <thead>
          <tr>
            <th text-align="center" width="5%"></th>
            <th width="5%">Nro</th>
            <th width="25%">Empresa</th>
            <th width="25%">Contacto</th>
            <th width="10%">Clientes</th>
            <th width="10%">Equipos</th>
            <th width="10%">Estado</th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th></th>
            <th>Nro</th>
            <th>Empresa</th>
            <th>Contacto</th>
            <th>Clientes</th>
            <th>Equipos</th>
            <th>Estado</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- Modal para Datos -->
<div class="modal fade" id="modal-subscriber">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div id="subscriber"></div>
    </div>
  </div>
</div>
<!-- /Modal para Datos -->

<!-- Modal para gestionar demo -->
<div class="modal fade" id="modal-demo-subscriber">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="fas fa-power-off"></i> Demo</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="POST" id="form_demo" role="form">
          <div class="row">
            <div class="col-12">
              <h4 id="label_subscriber"></h4>
            </div>
            <div class="form-group col-6" style="margin-top: 7mm">
              <div class="icheck-primary d-inline">
                  {!! Form::checkbox('demo', null, false, ['id'=>'demo']) !!}
                  <label for="demo">Suscriptor DEMO</label>
              </div>
            </div>
            <div class="form-group col-6" id="div_remaining_days">
              <label>Días demo *</label>
              {!! Form::number('remaining_days', null, ['id'=>'remaining_days', 'class'=>'form-control', 'placeholder'=>'', 'min'=>'0', 'step'=>'1' ,'required', 'lang'=>'en-150']) !!}
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_demo" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para gestionar demo -->

<!-- Modal para eliminar -->
<div class="modal fade" id="modal-delete-subscriber">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"><i class="far fa-trash-alt"></i> Eliminar Suscriptor</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="hdd_subscriber_id" value=""/>
        <p>Esta seguro que desea eliminar el suscriptor <b><span id="subscriber_name"></span></b> ?</p>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="btn_delete_subscriber" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<!-- /Modal para eliminar -->

@stop

@section('footer', 'Copyright © 2021. All rights reserved.' )

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
  
function showModalDemo(id){
  $.ajax({
    url: `{{URL::to("subscribers")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#label_subscriber').html(response.subscriber.name);
    $('#hdd_subscriber_id').val(response.subscriber.id);
    $('#remaining_days').val(response.subscriber.remaining_days);
    if(response.subscriber.demo){
      $('#demo').prop('checked', true);
      $('#div_remaining_days').show();
    }else{
      $('#demo').prop('checked', false);
      $('#div_remaining_days').hide();
    }
    $("#modal-demo-subscriber").modal("show");
  });
}

$('#demo').on('change', function(event) { 
  (event.target.checked)?$('#div_remaining_days').show():$('#div_remaining_days').hide();  
});

$('#btn_demo').click(function(event) {
  var validator = $("#form_demo" ).validate();
  formulario_validado = validator.form();
  if(formulario_validado){
    var id=$('#hdd_subscriber_id').val();
    $('#modal-demo-subscriber').modal('toggle');
    $.ajax({
      url: `{{URL::to("subscribers.demo")}}/${id}`,
      type: 'POST',
      data: {
        _token: "{{ csrf_token() }}", 
        date: $('#date').val(),
        subscriber_id: $('#hdd_subscriber_id').val(),
        demo:($('#demo').is(':checked'))?1:0,
        remaining_days: $('#remaining_days').val()
      },
    })
    .done(function(response) {
      $('#subscribers-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);      
    })
    .fail(function() {
      console.log("error");
    });
  }
});

function showModalSubscriber(id){
  url = '{{URL::to("subscribers.load")}}/'+id;
  $('#subscriber').load(url);
  $("#modal-subscriber").modal("show");
}

function change_status(id){
  $.ajax({
    url: `{{URL::to("subscribers.status")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#subscribers-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  
  
function showModalRevoke(subscriber_id, name){
  $('#hdd_subscriber_revoke_id').val(subscriber_id);
  $('#subscriber_revoke_name').html(name);
  $("#modalRevoke").modal("show");    
};

$("#btn_revoke_subscriber").on('click', function(event) {    
    revoke($('#hdd_subscriber_revoke_id').val());
});

function revoke(id){
  $.ajax({
    url: `{{URL::to("subscribers.revoke")}}/${id}`,
    type: 'GET',
  })
  .done(function(response) {
    $('#subscribers-table').DataTable().draw();
    toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function() {
    toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 2000);
  });
}  


function showModalDelete(subscriber_id, name){
  $('#hdd_subscriber_id').val(subscriber_id);
  $('#subscriber_name').html(name);
  $("#modal-delete-subscriber").modal("show");    
};
    
$("#btn_delete_subscriber").on('click', function(event) {    
  subscriber_delete($('#hdd_subscriber_id').val());
});

function subscriber_delete(id){  
  $.ajax({
      url: `{{URL::to("subscribers")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {
      $('#modal-delete-subscriber').modal('toggle');
      $('#subscribers-table').DataTable().draw();
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);

  })
  .fail(function(response) {
      //console.log("error");
      $('#modal-delete-subscriber').modal('toggle');
      toastr_msg('error', '{{ config('app.name') }}', response.message, 4000);
  });
}  

function subscriber_CRUD(id){
        
    var validator = $("#form_subscriber").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
        $('#btn_submit').attr('disabled', true);
        var form_data = new FormData($("#form_subscriber")[0]);
        $.ajax({
          url:(id==0)?'{{URL::to("subscribers")}}':'{{URL::to("subscribers")}}/'+id,
          type:'POST',
          cache:true,
          processData: false,
          contentType: false,      
          data: form_data
        })
        .done(function(response) {
          $('#btn_submit').attr('disabled', false);
          $('#modal-subscriber').modal('toggle');
          $('#subscribers-table').DataTable().draw(); 
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
    var table=$('#subscribers-table').DataTable({
        "oLanguage":{"sUrl":path_str_language},
        "aaSorting": [[1, "asc"]],
        processing: true,
        serverSide: true,
        ajax: {
            url: '{!! route('subscribers.datatable') !!}',
            type: "POST",
            data: function(d) {
                d._token= "{{ csrf_token() }}";
                d.demo=0;
            }
        },        
        columns: [
            { data: 'action', name: 'action', orderable: false, searchable: false},
            { data: 'number',   name: 'number', orderable: false, searchable: false},
            { data: 'name',   name: 'name', orderable: true, searchable: true},
            { data: 'contact',   name: 'full_name', orderable: false, searchable: true},
            { data: 'customers',   name: 'customers', orderable: true, searchable: false},
            { data: 'assets',   name: 'assets', orderable: true, searchable: false},
            { data: 'status', name: 'status', orderable: false, searchable: false }
        ]
    });
});
</script>
@stop