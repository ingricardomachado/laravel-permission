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
            @php($i=1)
            @foreach($customers as $customer)                    
            <tr>
                <td>{{ $customer->number }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ ($customer->main_contact)?$customer->main_contact->name:'' }}</td>
                <td>{{ $customer->cell }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->status_description }}</td>
            </tr>
            @endforeach
            </tbody>
</table>