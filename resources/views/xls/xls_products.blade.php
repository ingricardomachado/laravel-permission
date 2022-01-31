<table>
  <thead>
    <tr>
      <th>Código</th>
      <th>Nombre</th>
      <th>Stock</th>
      <th>Unidad</th>
      <th>Proveedor</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($products as $product)
      <tr>
        <td>{{ $product->number_mask }}</td>
        <td>{{ $product->name }}</td>
        <td>{{ $product->stock }}</td>
        <td>{{ $product->unit->name }}</td>
        <td>{{ $product->supplier->name }}</td>
        <td>{{ $product->status_description }}</td>
      </tr>
    @endforeach
  </tbody>
</table>