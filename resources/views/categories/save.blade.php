   
    <form action="{{url('categories/'.$category->id)}}" id="form_category" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('subscriber_id', ($category->id)?$category->subscriber_id:session('subscriber')->id, ['id'=>'subscriber_id']) !!}
        {!! Form::hidden('category_id', ($category->id)?$category->id:0, ['id'=>'category_id']) !!}
        @if($category->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-th-large"></i> {{ ($category->id) ? "Modificar Categoría": "Registrar Categoría" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">                            
                <div class="form-group col-sm-12">
                    <label>Nombre *</label>
                    {!! Form::text('name', $category->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="category_CRUD({{ ($category->id)?$category->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>

$(document).ready(function() {            
    $("#type").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-category .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $('#category').focus();
});

</script>