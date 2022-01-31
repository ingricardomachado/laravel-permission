@extends('layouts.blank_report_header')

@push('stylesheets')

@endpush

@section('content')
        <h2 class="text-center">Categorías</h2>
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
                    @foreach($categories as $category)                    
                    <tr>
                        <td class="text-left">{{ $category->name }}</td>
                        <td class="text-left">{{ $category->status_description }}</td>
                    </tr>
                    @endforeach
                    </tbody>
        </table>
        <br/>
        <br/>    
@endsection

