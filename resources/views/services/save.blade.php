   
    <form action="{{url('services/'.$service->id)}}" id="form_service" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('subscriber_id', ($service->id)?$service->subscriber_id:session('subscriber')->id, ['id'=>'subscriber_id']) !!}        
        {!! Form::hidden('service_id', ($service->id)?$service->id:0, ['id'=>'service_id']) !!}
        @if($service->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-tools"></i> {{ ($service->id) ? "Modificar Servicio": "Registrar Servicio" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row pre-scrollable" style="max-height: 70vh">                            
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('name', $service->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">  
                  <label> Categoría *</label>
                  {{ Form::select('category', $categories, ($service->id)?$service->category_id:0, ['id'=>'category', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Precio {{ session('coin') }} *</label>
                    {!! Form::number('price', $service->price, ['id'=>'price', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>                
                <div class="form-group col-sm-6">
                    <label>Descripción *</label>
                    {!! Form::textarea('description', $service->description, ['id'=>'description', 'class'=>'form-control', 'rows'=>'2', 'style'=>'font-size:14px', 'placeholder'=>'', 'maxlength'=>'200', 'required']) !!}
                </div>                
                <div class="form-group col-sm-6">
                    <label title="Unidad de medida para facturación electrónica">Unidad SAT</label>
                    {!! Form::text('unit_fe', $service->unit_fe, ['id'=>'unit_fe', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Código SAT</label>
                    {!! Form::text('code_fe', $service->code_fe, ['id'=>'code_fe', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="service_CRUD({{ ($service->id)?$service->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>

$(document).ready(function() {            
   
    $("#category").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-service .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $('#service').focus();
});

</script>