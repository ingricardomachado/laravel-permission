<table>
    <thead>
        <tr>
          <th>Código</th>
          <th>Fecha</th>
          <th>Cliente</th>
          <th>Monto</th>
          <th>Folio</th>
          <th>Forma pago</th>
          <th>Metodo pago</th>
          <th>Condición pago</th>
          <th>Plazo días</th>
          <th>Balance</th>
          <th>Fecha cierre</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receivables as $receivable)                    
        <tr>
            <td>{{ $receivable->number }}</td>
            <td>{{ $receivable->date->format('d/m/Y') }}</td>
            <td>{{ $receivable->customer->name }}</td>
            <td>{{ money_fmt($receivable->amount) }}</td>
            <td>{{ $receivable->folio }}</td>
            <td>{{ $receivable->way_pay_description }}</td>
            <td>{{ $receivable->method_pay_description }}</td>
            <td>{{ $receivable->condition_pay_description }}</td>
            <td>{{ $receivable->days }}</td>
            <td>{{ $receivable->balance }}</td>
            <td>{{ $receivable->close_date->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
