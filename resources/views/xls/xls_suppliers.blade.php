<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Empresa</th>
            <th>Contacto</th>
            <th>Celular</th>
            <th>Teléfono</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($suppliers as $supplier)                    
        <tr>
            <td>{{ $supplier->number }}</td>
            <td>{{ $supplier->name }}</td>
            <td>{{ ($supplier->main_contact)?$supplier->main_contact->name:'' }}</td>
            <td>{{ $supplier->cell }}</td>
            <td>{{ $supplier->phone }}</td>
            <td>{{ $supplier->status_description }}</td>
        </tr>
        @endforeach
    </tbody>
</table>