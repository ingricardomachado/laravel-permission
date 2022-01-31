@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Productos</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left" width="10%">Código</th>
                        <th class="text-left" width="30%">Nombre</th>
                        <th class="text-left" width="15%">Stock</th>
                        <th class="text-left" width="15%">Unidad</th>
                        <th class="text-left" width="20%">Proveedor</th>
                        <th class="text-left" width="10%">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($products as $product)                    
                    <tr>
                        <td class="text-left">{{ $product->number_mask }}</td>
                        <td class="text-left">
                            {{ $product->name }}<br>
                            <small>{{ $product->category->name }}</small>
                        </td>
                        <td class="text-left">{{ $product->stock }}</td>
                        <td class="text-left">{{ $product->unit->name }}</td>
                        <td class="text-left">{{ $product->supplier->name }}</td>
                        <td class="text-left">{{ $product->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
@endsection

