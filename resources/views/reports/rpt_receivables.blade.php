@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Cuentas por Cobrar</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                <tr>
                  <th class="text-left" width="5%">Código</th>
                  <th class="text-left" width="10%">Fecha</th>
                  <th class="text-left" width="30%">Cliente</th>
                  <th class="text-left" width="10%">Monto</th>
                  <th class="text-left" width="10%">Folio</th>
                  <th class="text-left" width="10%">Forma pago</th>
                  <th class="text-left" width="10%">Metodo pago</th>
                  <th class="text-left" width="10%">Condición pago</th>
                  <th class="text-left" width="10%">Plazo días</th>
                  <th class="text-left" width="10%">Balance</th>
                  <th class="text-left" width="10%">Fecha cierre</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receivables as $receivable)                    
                <tr>
                    <td class="text-left">{{ $receivable->number }}</td>
                    <td class="text-left">{{ $receivable->date->format('d/m/Y') }}</td>
                    <td class="text-left">{{ $receivable->customer->name }}</td>
                    <td class="text-left">{{ money_fmt($receivable->amount) }}</td>
                    <td class="text-left">{{ $receivable->folio }}</td>
                    <td class="text-left">{{ $receivable->way_pay_description }}</td>
                    <td class="text-left">{{ $receivable->method_pay_description }}</td>
                    <td class="text-left">{{ $receivable->condition_pay_description }}</td>
                    <td class="text-left">{{ $receivable->days }}</td>
                    <td class="text-left">{{ $receivable->balance }}</td>
                    <td class="text-left">{{ $receivable->close_date->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
@endsection

