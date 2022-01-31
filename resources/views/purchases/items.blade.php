<div class="table-responsive">
    <table class="table table-small" width="100%">
        <thead>
            <tr>
                <th width="10%"></th>
                <th width="10%"  align="center">Cantidad</th>
                <th width="50%" align="left">Descripción</th>
                <th width="15%" class="text-right">C.U.</th>
                <th width="15%" class="text-right">Sub Total</th>
            </tr>
        </thead>
        <tbody>
            @for ($i=0; $i < sizeof($array_descriptions) ; $i++)
            <tr style="font-size: 10pt">
                <td align="center">
                    <div class="input-group">
                        <a href="#" class="text-muted" onclick="edit_item('{{ $i }}')" title="Modificar item"><i class="far fa-edit"></i></a>&nbsp;
                        <a href="#" class="text-muted" onclick="remove_item('{{ $i }}')" title="Eliminar item"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
                <td align="center">{{ money_fmt($array_quantities[$i]) }}</td>
                <td align="left">{{ $array_descriptions[$i] }}</td>
                <td align="right">{{ money_fmt($array_unit_prices[$i]) }}</td>
                <td align="right">{{ money_fmt($array_sub_totals[$i]) }}</td>
            </tr>
            @endfor
        </tbody>
    </table>
</div>

<script>
   
$(document).ready(function(){
  
    $('#sub_total').html('{{ money_fmt($sub_total) }}');
    $('#total_discount').html('{{ money_fmt($total_discount) }}');
    $('#total_tax').html('{{ money_fmt($total_tax) }}');
    $('#total').html('{{ money_fmt($total) }}');
    
});
</script>