@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Empleados</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left" width="10%">Código</th>
                        <th class="text-left" width="25%">Nombre</th>
                        <th class="text-left" width="25%">Celular</th>
                        <th class="text-left" width="15%">Teléfono</th>
                        <th class="text-left" width="15%">Rol</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($employees as $employee)                    
                    <tr>
                        <td class="text-left">{{ $employee->number_mask }}</td>
                        <td class="text-left">
                            {{ $employee->full_name }}<br>
                            <small><i>{{ $employee->email }}</i></small>
                        </td>
                        <td class="text-left">{{ $employee->cell }}</td>
                        <td class="text-left">{{ $employee->phone }}</td>
                        <td class="text-left">{{ $employee->user->role_description ?? '' }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

