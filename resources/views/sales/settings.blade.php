@extends('adminlte::page')

@section('plugins.Summernote', true)
@section('plugins.ICheck', true)

@section('content_header')
@stop

@section('content')
<div class="card">
  <div class="card-header">
    <h3 class="card-title">
    <i class="fas fa-cogs" aria-hidden="true"></i> Configuraciones</h3>
  </div>
  <div class="card-body">
      <form action="#" id="form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        <div class="col-12 mb-1">
            <div class="icheck-primary d-inline">
                {!! Form::checkbox('coin_name', null, $setting->show_coin_name, ['id'=>'coin_name']) !!}
                <label for="coin_name"><small>Mostrar nombre de moneda al imprimir</small></label>
            </div>                    
        </div>
        <div class="form-group col-12">
          <small><b>Términos y condiciones predefinidas</b> Max. 1500 caracteres.</small>
          <span class="counter_conditions small text-light pull-right" style="font-size: 10px"></span>
          {!! Form::textarea('conditions', $setting->conditions, ['id'=>'conditions', 'class'=>'form-control', 'placeholder'=>'', 'maxlength'=>'1500']) !!}
        </div>
      </form>
      <div class="col-12 text-right">
        <button type="button" onclick="update_settings()" id="btn_update" class="btn btn-sm btn-primary">Guardar</button>
        <a href="{{ url('sales') }}" class="btn btn-sm btn-default" title=""><i class="fa fa-hand-o-left" aria-hidden="true"></i> Regresar</a>        
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
  
function update_settings(){
    var validator = $("#form").validate();
    formulario_validado = validator.form();
    if(formulario_validado){
      var id = {{ $setting->id }};
      var form_data = new FormData($("#form")[0]);
      form_data.append('show_coin_name', ($('#coin_name').is(":checked"))?1:0);
      $.ajax({
        url:'{{URL::to("sales.update_settings")}}/'+id,
        type:'POST',
        cache:true,
        processData: false,
        contentType: false,      
        data: form_data
      })
      .done(function(response) {
        toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
      })
      .fail(function(response) {
        if(response.status == 422){
          var errorsHtml='';
          $.each(response.responseJSON.errors, function (key, value) {
            errorsHtml += '<li>' + value[0] + '</li>'; 
          });          
          toastr_msg('error', '{{ config('app.name') }}', errorsHtml, 4000);
        }else{
          toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
        }
      });
    }
}

$(document).ready(function() {

  $('#conditions').summernote({
    lang: "es-ES",
    placeholder: 'Escribe aqui terminos y condiciones predefinidas ...',
    height: 150,
    lineHeights: ['0.2', '0.3', '0.4', '0.5', '0.6', '0.8', '1.0', '1.2', '1.4', '1.5', '2.0', '3.0'],    
    toolbar: [
        // [groupName, [list of button]]
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['fontsize', ['fontsize']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']]
      ],
      callbacks: {
        onKeydown: function (e) {
            var t = e.currentTarget.innerText;
            if (t.trim().length >= 1500) {
                if (e.keyCode != 8)
                    e.preventDefault();
            }
        },
        onKeyup: function (e) {
            var t = e.currentTarget.innerText;
            $('.counter_conditions').text(1500 - t.trim().length);
        },
        onPaste: function (e) {
            var t = e.currentTarget.innerText;
            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
            e.preventDefault();
            var all = t + bufferText;
            if(all.length > 1500) {       
                document.execCommand('insertText', false, bufferText.trim().substring(0, 1500 - t.length));
                $('.counter_conditions').text(all.trim().substring(0, 1500));
            }else {
                document.execCommand('insertText', false, bufferText);
                $('.counter_conditions').text(all);
            }
        }
      },
  });

});    
</script>
@stop