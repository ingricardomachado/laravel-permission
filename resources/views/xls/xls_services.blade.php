<table>
  <thead>
    <tr>
      <th>Código</th>
      <th>Nombre</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($services as $service)
      <tr>
        <td>{{ $service->number_mask }}</td>
        <td>{{ $service->name }}</td>
        <td>{{ $service->status_description }}</td>
      </tr>
    @endforeach
  </tbody>
</table>