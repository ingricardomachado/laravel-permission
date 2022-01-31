@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Clientes</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left" width="10%">Código</th>
                        <th class="text-left" width="25%">Empresa</th>
                        <th class="text-left" width="25%">Contacto</th>
                        <th class="text-left" width="15%">Celular</th>
                        <th class="text-left" width="15%">Teléfono</th>
                        <th class="text-left" width="15%">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($customers as $customer)                    
                    <tr>
                        <td class="text-left">{{ $customer->number }}</td>
                        <td class="text-left">{{ $customer->name }}</td>
                        <td class="text-left">{{ ($customer->main_contact)?$customer->main_contact->name:'' }}</td>
                        <td class="text-left">{{ $customer->cell }}</td>
                        <td class="text-left">{{ $customer->phone }}</td>
                        <td class="text-left">{{ $customer->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

