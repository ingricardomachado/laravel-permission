   
    <form action="{{url('receivables/'.$receivable->id)}}" id="form_receivable" method="POST">
        <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
        {!! Form::hidden('receivable_id', ($receivable->id)?$receivable->id:0, ['id'=>'receivable_id']) !!}
        @if($receivable->id)                
            {{ Form::hidden ('_method', 'PUT') }}
        @endif
        <div class="modal-header">
          <h5 class="modal-title"><i class="far fa-money-bill-alt"></i> {{ ($receivable->id) ? "Modificar Cuenta por Cobrar": "Registrar Cuenta por Cobrar" }} <small> Complete el formulario <b>(*) Campos obligatorios.</b></small></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="row">                            
                <div class="form-group col-sm-12">  
                  <label> Cliente *</label>
                  {{ Form::select('customer', $customers, ($receivable->id)?$receivable->customer_id:null, ['id'=>'customer', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-4">
                  <label>Fecha *</label>
                    <div class="input-group date" id="calendar_date" data-target-input="nearest">
                        <input type="text" name="date" id="date" value="{{ ($receivable->date)?$receivable->date->format('d/m/Y'):'' }}" class="form-control datetimepicker-input" data-target="#calendar_date"/>
                        <div class="input-group-append" data-target="#calendar_date" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-4">
                    <label>Folio *</label>
                    {!! Form::text('folio', $receivable->folio, ['id'=>'folio', 'class'=>'form-control', 'type'=>'text', 'placeholder'=>'', 'maxlength'=>'10', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">
                   <label>Monto *</label>
                  {!! Form::number('amount', $receivable->amount, ['id'=>'amount', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-12">
                    <label>Descripción *</label>
                    {!! Form::textarea('description', $receivable->description, ['id'=>'description', 'class'=>'form-control', 'rows'=>'2', 'style'=>'font-size:14px', 'placeholder'=>'', 'maxlength'=>'200', 'required']) !!}
                </div>
                <div class="form-group col-sm-4">  
                  <label> Forma de pago *</label>
                  {{ Form::select('way_pay', ['1'=>'Efectivo', '2'=>'Cheque', '3'=>'Tarjeta', '4'=>'Transferencia'], $receivable->way_pay, ['id'=>'way_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-4">  
                  <label> Método de pago *</label>
                  {{ Form::select('method_pay', ['1'=>'Pago total', '2'=>'Pagos parciales'], $receivable->method_pay, ['id'=>'method_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-4">  
                  <label> Condición de pago *</label>
                  {{ Form::select('condition_pay', ['1'=>'Contado', '2'=>'Crédito'], $receivable->condition_pay, ['id'=>'condition_pay', 'class'=>'select2 form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                </div>
                <div class="form-group col-sm-4">
                   <label>Dias de plazo *</label>
                  {!! Form::number('days', $receivable->days, ['id'=>'days', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'1', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-4">
                   <label>Balance *</label>
                  {!! Form::number('balance', $receivable->balance, ['id'=>'balance', 'class'=>'form-control', 'placeholder'=>'', 'min' => '0', 'step'=>'0.01', 'required', 'lang'=>'en-150']) !!}
                </div>
                <div class="form-group col-sm-4">
                  <label>Fecha de cierre *</label>
                    <div class="input-group date" id="calendar_close_date" data-target-input="nearest">
                        <input type="text" name="close_date" id="close_date" value="{{ ($receivable->close_date)?$receivable->close_date->format('d/m/Y'):'' }}" class="form-control datetimepicker-input" data-target="#calendar_close_date"/>
                        <div class="input-group-append" data-target="#calendar_close_date" data-toggle="datetimepicker">
                            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" id="btn_submit" onclick="receivable_CRUD({{ ($receivable->id)?$receivable->id:0 }})" class="btn btn-sm btn-primary">Guardar</button>
            <button type="button" id="btn_close" class="btn btn-sm btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </form>
<script>

$(document).ready(function() {            
   
    //Date range picker
    $('#calendar_date').datetimepicker({
      locale:'es',
      format: 'DD/MM/YYYY',
    });
    
    $('#calendar_close_date').datetimepicker({
      locale:'es',
      format: 'DD/MM/YYYY',
    });
    
    $("#customer").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-receivable .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $("#way_pay").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-receivable .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $("#method_pay").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-receivable .modal-content'),
        allowClear: false,
        width: '100%'
    });

    $("#condition_pay").select2({
        language: "es",
        placeholder: "Seleccione",
        minimumResultsForSearch: 10,
        dropdownParent: $('#modal-receivable .modal-content'),
        allowClear: false,
        width: '100%'
    });
    
    $('#receivable').focus();
});

</script>