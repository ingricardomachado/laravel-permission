<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Celular</th>
            <th>Teléfono</th>
            <th>Rol</th>
        </tr>
    </thead>
    <tbody>
        @foreach($employees as $employee)                    
        <tr>
            <td>{{ $employee->number_mask }}</td>
            <td>{{ $employee->full_name }}</td>
            <td>{{ $employee->cell }}</td>
            <td>{{ $employee->phone }}</td>
            <td>{{ $employee->user->role_description ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>