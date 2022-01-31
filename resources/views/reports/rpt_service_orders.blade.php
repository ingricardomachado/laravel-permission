@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
    <h2 class="text-center">Ordenes de Servicio</h2>
    <!-- Body -->
    <table class="table" width="100%">
        <thead>
            <tr>
                <th class="text-left" width="10%">Folio</th>
                <th class="text-left" width="20%">Fecha</th>
                <th class="text-left" width="20%">Cliente</th>
                <th class="text-left" width="20%">Servicio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($service_orders as $service_order)                    
            <tr>
                <td class="text-left">{{ $service_order->folio_mask }}</td>
                <td class="text-left">{{ $service_order->date->format('d/m/Y H:i') }}</td>
                <td class="text-left">{{ $service_order->customer->name }}</td>
                <td class="text-left">{{ $service_order->service->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection

