<table>
  <thead>
    <tr>
      <th>Folio</th>
      <th>Fecha</th>
      <th>Cliente</th>
      <th>Servicio</th>
    </tr>
  </thead>
  <tbody>
    @foreach($service_orders as $service_order)                    
    <tr>
      <td>{{ $service_order->folio_mask }}</td>
      <td>{{ $service_order->date->format('d/m/Y H:i') }}</td>
      <td>{{ $service_order->customer->name }}</td>
      <td>{{ $service_order->service->name }}</td>
    </tr>
    @endforeach
  </tbody>
</table>