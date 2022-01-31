@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Proveedores</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left" width="10%">Código</th>
                        <th class="text-left" width="25%">Empresa</th>
                        <th class="text-left" width="25%">Contacto</th>
                        <th class="text-left" width="15%">Teléfono</th>
                        <th class="text-left" width="15%">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($suppliers as $supplier)                    
                    <tr>
                        <td class="text-left">{{ $supplier->number }}</td>
                        <td class="text-left">{{ $supplier->name }}</td>
                        <td class="text-left">{{ ($supplier->main_contact)?$supplier->main_contact->name:'' }}</td>
                        <td class="text-left">{{ $supplier->phone }}</td>
                        <td class="text-left">{{ $supplier->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

