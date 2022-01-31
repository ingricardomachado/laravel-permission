   
    <form action="{{url('products/'.$product->id)}}" id="form_product" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('subscriber_id', ($product->id)?$product->subscriber_id:session('subscriber')->id, ['id'=>'subscriber_id']) !!}
        {!! Form::hidden('product_id', ($product->id)?$product->id:0, ['id'=>'product_id']) !!}
        @if($product->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-box-open"></i> {{ ($product->id) ? "Modificar Producto": "Registrar Producto" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row pre-scrollable" style="max-height: 70vh">                            
                <div class="form-group col-sm-6">
                    <label>Nombre *</label>
                    {!! Form::text('name', $product->name, ['id'=>'name', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">  
                  <label> Categoría *</label>
                  {{ Form::select('category', $categories, ($product->id)?$product->category_id:0, ['id'=>'category', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Código de fábrica *</label>
                    {!! Form::text('code', $product->code, ['id'=>'code', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Descripción *</label>
                    {!! Form::textarea('description', $product->description, ['id'=>'description', 'class'=>'form-control', 'rows'=>'2', 'style'=>'font-size:14px', 'placeholder'=>'', 'maxlength'=>'200', 'required']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Precio {{ session('coin') }} *</label>
                    {!! Form::number('price', $product->price, ['id'=>'price', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Costo {{ session('coin') }}</label>
                    {!! Form::number('cost', $product->cost, ['id'=>'cost', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Modelo/Nombre</label>
                    {!! Form::text('model', $product->model, ['id'=>'model', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">  
                  <label>Unidad *</label>
                  {{ Form::select('unit', $units, ($product->id)?$product->unit_id:0, ['id'=>'unit', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Proveedor </label>
                    {{ Form::select('supplier', $suppliers, ($product->id)?$product->supplier_id:0, ['id'=>'supplier', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>''])}}
                </div>
                <div class="form-group col-sm-6">
                    <label>Fabricante/Marca</label>
                    {!! Form::text('make', $product->make, ['id'=>'make', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>SKU</label>
                    {!! Form::text('sku', $product->sku, ['id'=>'sku', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'100']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Punto de Pedido *</label>
                    {!! Form::number('reorder_point', ($product->id)?$product->reorder_point:1, ['id'=>'reorder_point', 'class'=>'form-control', 'placeholder'=>'', 'step'=>'1', 'min' => '1', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Stock Inicial *</label>
                    {!! Form::number('initial_stock', ($product->id)?$product->initial_stock:1, ['id'=>'initial_stock', 'class'=>'form-control', 'placeholder'=>'', 'step'=>'1', 'min' => '1', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label>Stock Mínimo *</label>
                    {!! Form::number('safety_stock', ($product->id)?$product->safety_stock:1, ['id'=>'safety_stock', 'class'=>'form-control', 'placeholder'=>'', 'step'=>'1', 'min' => '1', 'required', 'lang'=>'en-150']) !!}
                </div>                
                <div class="form-group col-sm-6">
                    <label title="Código para facturación electrónica">Código SAT</label>
                    {!! Form::text('code_fe', $product->code_fe, ['id'=>'code_fe', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20']) !!}
                </div>
                <div class="form-group col-sm-6">
                    <label title="Unidad de medida para facturación electrónica">Unidad SAT</label>
                    {!! Form::text('unit_fe', $product->unit_fe, ['id'=>'unit_fe', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'20']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Foto</label><small> (Sólo formatos jpeg, jpg, bmp, png. Máx. 10Mb.)</small>
                    <input type="file" name="photo" id="photo" class="form-control">
                </div>
                <div class="form-group col-sm-12">
                    <label>Documentos</label><small> (Sólo formatos pdf, xls, xlsx, doc, docs, odt, ods, jpeg, jpg, bmp, png. Máx. 10Mb.)</small>
                    <div class="input-group hdtuto control-group lst increment" >
                      <input type="file" name="filenames[]" class="myfrm form-control">
                      <div class="input-group-btn">
                        <button class="btn btn-primary btn-clone" type="button"><i class="fas fa-file-medical"></i> Agregar mas documentos</button>
                      </div>
                    </div>
                </div>
                <div class="form-group col-sm-12" style="display:{{ ($product->documents()->count()>0)?'solid':'none' }}">
                    @foreach($product->documents()->get() as $document)
                        <div class="product-document">
                            <a href="{{ route('product_documents.download', $document->id) }}" title="Click para descargar">{{ $document->file_name }}</a> <a href="#" class="href-delete-document" data-id="{{ $document->id }}" title="Eliminar documento" onclick="document_delete({{ $document->id }})"><i class="far fa-trash-alt"></i></a>
                        </div>                        
                    @endforeach
                </div>

                <div class="clone" style="display:none">
                  <div class="hdtuto control-group lst input-group" style="margin-top:10px">
                    <input type="file" name="filenames[]" class="myfrm form-control">
                    <div class="input-group-btn">
                      <button class="btn btn-danger" type="button"><i class="far fa-trash-alt"></i></button>
                    </div>
                  </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="product_CRUD({{ ($product->id)?$product->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>

$("body").on("click",".href-delete-document",function(){
    $(this).parents(".product-document").remove();
});

function document_delete(id){  
  $.ajax({
      url: `{{URL::to("product_documents")}}/${id}`,
      type: 'DELETE',
      data: {
        _token: "{{ csrf_token() }}", 
      },
  })
  .done(function(response) {      
      toastr_msg('success', '{{ config('app.name') }}', response.message, 2000);
  })
  .fail(function(response) {
      toastr_msg('error', '{{ config('app.name') }}', response.responseJSON.message, 4000);
  });
}  

$(document).ready(function() {            

    $(".btn-clone").click(function(){
        var lsthmtl = $(".clone").html();
        $(".increment").after(lsthmtl);
    });

    $("body").on("click",".btn-danger",function(){
        $(this).parents(".hdtuto").remove();
    });
    
    $("#unit").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-product .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $("#supplier").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-product .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $("#category").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-product .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $('#product').focus();
});

</script>