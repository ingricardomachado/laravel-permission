@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Unidades</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-center" width="20%">Unidad</th>
                        <th class="text-left" width="40%">Nombre</th>
                        <th class="text-left">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($units as $unit)                    
                    <tr>
                        <td class="text-center">{{ $unit->unit }}</td>
                        <td class="text-left">{{ $unit->name }}</td>
                        <td class="text-left">{{ $unit->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
@endsection

