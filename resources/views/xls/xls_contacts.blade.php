<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Celular</th>
            <th>Teléfono</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($contacts as $contact)                    
        <tr>
            <td>{{ $contact->number }}</td>
            <td>{{ $contact->full_name }}</td>
            <td>{{ $contact->email }}</td>
            <td>{{ $contact->cell }}</td>
            <td>{{ $contact->phone }}</td>
            <td>{{ $contact->status_description }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
