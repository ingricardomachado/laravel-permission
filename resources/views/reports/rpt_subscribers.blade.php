@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Suscriptores {{ ($demo)?'demo':'permanentes' }}</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-center" width="5%">Nro</th>
                        <th class="text-left" width="25%">Empresa</th>
                        <th class="text-left" width="25%">Contacto</th>
                        <th class="text-center">Clientes</th>
                        <th class="text-center">Equipos</th>
                        <th class="text-left">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($subscribers as $subscriber)                    
                    <tr>
                        <td class="text-center"><b>{{ $subscriber->number }}</b></td>
                        <td class="text-left">
                            <b>{{ $subscriber->bussines_name }}</b><br>
                            <small>
                                PIN {{ $subscriber->user->PIN }}<br>
                                {{ $subscriber->created_at->format('d/m/Y') }}
                            </small>
                        </td>
                        <td class="text-left">
                            {{ $subscriber->name }}<br>
                            <small><i>
                                {{ $subscriber->email }}<br>
                                {{ $subscriber->cell }}
                            </i></small>
                        </td>
                        <td class="text-center">{{ $subscriber->customers()->count() }}</td>
                        <td class="text-center"></td>
                        <td class="text-left">{{ $subscriber->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
@endsection

