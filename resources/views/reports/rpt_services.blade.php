@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Servicios</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left" width="10%">Código</th>
                        <th class="text-left" width="30%">Nombre</th>
                        <th class="text-left" width="10%">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($services as $service)                    
                    <tr>
                        <td class="text-left">{{ $service->number_mask }}</td>
                        <td class="text-left">
                            {{ $service->name }}<br>
                            <small>{{ $service->category->name }}</small>
                        </td>
                        <td class="text-left">{{ $service->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
@endsection

