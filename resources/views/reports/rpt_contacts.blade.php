@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Contactos</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left" width="10%">Código</th>
                        <th class="text-left" width="30%">Nombre</th>
                        <th class="text-left" width="25%">Celular</th>
                        <th class="text-left" width="15%">Teléfono</th>
                        <th class="text-left" width="15%">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($contacts as $contact)                    
                    <tr>
                        <td class="text-left">{{ $contact->number }}</td>
                        <td class="text-left">
                            <b>{{ $contact->full_name }}</b><br>
                            <small><i>{{ $contact->email }}</i></small>
                        </td>
                        <td class="text-left">{{ $contact->cell }}</td>
                        <td class="text-left">{{ $contact->phone }}</td>
                        <td class="text-left">{{ $contact->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

