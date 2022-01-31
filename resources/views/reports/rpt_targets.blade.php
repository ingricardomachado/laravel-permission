@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Giros</h2>
        <!-- Body -->
        <table class="table" width="100%">
            <thead>
                    <tr>
                        <th class="text-left" width="80%">Nombre</th>
                        <th class="text-left" width="20%">Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($targets as $target)                    
                    <tr>
                        <td class="text-left">{{ $target->name }}</td>
                        <td class="text-left">{{ $target->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

