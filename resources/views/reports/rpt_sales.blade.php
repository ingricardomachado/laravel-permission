@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">{{ ($type=='F')?'Facturas':'Cotizaciones' }}</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left" width="10%">Folio</th>
                        <th class="text-left" width="25%">Fecha</th>
                        <th class="text-left" width="25%">Vencimiento</th>
                        <th class="text-left" width="15%">Cliente</th>
                        <th class="text-left" width="15%">Prospecto</th>
                        <th class="text-right" width="15%">Monto</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($sales as $sale)                    
                    <tr>
                        <td class="text-left">{{ $sale->folio }}</td>
                        <td class="text-left">{{ $sale->date->format('d/m/Y') }}</td>
                        <td class="text-left">{{ ($sale->due_date)?$sale->due_date->format('d/m/Y'):'' }}</td>
                        <td class="text-left">{{ ($sale->customer_id)?$sale->customer->name:'' }}</td>
                        <td class="text-left">{{ $sale->prospect }}</td>
                        <td class="text-right">{{ session('coin') }}{{ money_fmt($sale->total) }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

