@extends('adminlte::page')

@section('title', 'Dashboard')

@section('plugins.Select2', true)
@section('plugins.KartikFileinput', true)


@section('content_header')
@stop

@section('content')

<h2>Hola! {{ session()->get('role') }}</h2>


@stop

@section('footer', 'Copyright © 2021. All rights reserved.')

@section('css')

@stop

@section('js')
<script>


$(document).ready(function(){          
                
    if(false){
      $("#modal-register-subscriber").modal("show");
    }

    $("#state").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-register-subscriber .modal-content'),
        allowClear: false,
        width: '100%'
    });

	$('#logo').fileinput({
		language: 'es',
		allowedFileExtensions : ['jpg', 'jpeg', 'png'],
		previewFileIcon: "<i class='fas fa-exclamation-triangle'></i>",
		initialPreviewAsData: true,
		showUpload: false,
		showCancel: false,        
		maxFileSize: 2000,
		maxFilesNum: 1,
		overwriteInitial: true,
		progressClass: false,
		progressCompleteClass: false,
	});

	$('.kv-upload-progress').hide();            


});	
</script>
@stop