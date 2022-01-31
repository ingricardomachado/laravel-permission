<div class="table-responsive">
    <table class="table table-small" width="100%">
        <thead>
            <tr>
                <th width="10%"></th>
                <th width="5%" align="center" title="Cantidad">Cant.</th>
                <th width="10%" align="left">Código</th>
                <th width="30%" align="left">Descripción</th>
                <th width="10%" class="text-right" title="Precio de Lista">P.L</th>
                <th width="10%" class="text-right">Desct.</th>
                <th width="10%" class="text-right" title="Precio Unitario">P.U</th>
                <th width="15%" class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @for ($i=0; $i < sizeof($array_descriptions) ; $i++)
            @php
                $list_price=$array_unit_prices[$i]*(1-$array_percent_discounts[$i]/100);
            @endphp
            <tr style="font-size: 10pt">
                <td align="center">
                    <div class="input-group">
                        <a href="#" class="text-muted" onclick="edit_item('{{ $i }}')" title="Modificar item"><i class="far fa-edit"></i></a>&nbsp;
                        <a href="#" class="text-muted" onclick="remove_item('{{ $i }}')" title="Eliminar item"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
                <td align="center">{{ money_fmt($array_quantities[$i]) }}</td>
                <td align="left">{{ $array_codes[$i] }}</td>
                <td align="left">{{ $array_descriptions[$i] }}</td>
                <td align="right">{{ session('coin') }}{{ money_fmt($array_unit_prices[$i]) }}</td>
                <td align="right">{{ money_fmt($array_percent_discounts[$i]) }}%</td>
                <td align="right">{{ session('coin') }}{{ money_fmt($list_price) }}</td>
                <td align="right">{{ session('coin') }}{{ money_fmt($array_sub_totals[$i]-$array_discounts[$i]) }}</td>
            </tr>
            @endfor
        </tbody>
    </table>
</div>

<script>
   
$(document).ready(function(){
  
    $('#sub_total').html('{{ money_fmt($sub_total) }}');
    $('#total_tax').html('{{ money_fmt($total_tax) }}');
    $('#total').html('{{ money_fmt($total) }}');
    
});
</script>