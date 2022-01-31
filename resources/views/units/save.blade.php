   
    <form action="{{url('units/'.$unit->id)}}" id="form_unit" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('unit_id', ($unit->id)?$unit->id:0, ['id'=>'unit_id']) !!}
        @if($unit->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
          <h5 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> {{ ($unit->id) ? "Modificar Unidad": "Registrar Unidad" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">                            
                <div class="form-group col-sm-4">
                    <label>Unidad *</label>
                    {!! Form::text('unit', $unit->unit, ['id'=>'unit', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'required']) !!}
                </div>
                <div class="form-group col-sm-8">
                    <label>Nombre *</label>
                    {!! Form::text('name', $unit->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="unit_CRUD({{ ($unit->id)?$unit->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>

$(document).ready(function() {            
    $('#unit').focus();
});

</script>