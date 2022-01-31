<table>
  <thead>
    <tr>
      <th>Folio</th>
      <th>Fecha</th>
      <th>Vencimiento</th>
      <th>Cliente</th>
      <th>Prospecto</th>
      <th>Monto</th>
    </tr>
  </thead>
  <tbody>
    @php($i=1)
    @foreach($sales as $sale)                    
    <tr>
      <td>{{ $sale->folio }}</td>
      <td>{{ $sale->date->format('d/m/Y') }}</td>
      <td>{{ ($sale->due_date)?$sale->due_date->format('d/m/Y'):'' }}</td>
      <td>{{ ($sale->customer_id)?$sale->customer->name:'' }}</td>
      <td>{{ $sale->prospect }}</td>
      <td>{{ session('coin') }}{{ money_fmt($sale->total) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>