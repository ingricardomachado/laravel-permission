<table class="table" style="font-size: 10pt;">
	<thead>
		<tr>
			<th></th>
			<th>Nombre</th>
			<th>Título</th>
			<th>Area</th>
			<th>Celular</th>
			<th>Correo</th>
			<th>Principal</th>
		</tr>
	</thead>
	<tbody>		
        @for ($i=0; $i < sizeof($array_names) ; $i++)		
		<tr>
            <td align="center">
                <div class="input-group">
                    <a href="#" class="text-muted" onclick="edit_contact('{{ $i }}')" title="Modificar item"><i class="far fa-edit"></i></a>&nbsp;
                    @if(!$array_mains[$i])
                    	<a href="#" class="text-muted" onclick="remove_contact('{{ $i }}')" title="Eliminar item"><i class="fas fa-trash"></i></a>
                    @endif
                </div>
            </td>
			<td>{{ $array_names[$i] }}</td>
			<td>{{ ($array_occupations[$i])?$array_occupations[$i]:'' }}</td>
			<td>{{ ($array_positions[$i])?$array_positions[$i]:'' }}</td>
			<td>{{ $array_phones[$i] }}</td>
			<td>{{ ($array_emails[$i])?$array_emails[$i]:'' }}</td>
			<td>{{ ($array_mains[$i])?'Si':'No' }}</td>
		</tr>
		@endfor
	</tbody>
</table>