@component('mail::layout')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ $sale->subscriber->name }}
    @endcomponent
@endslot

# Estimado cliente {{ $sale->customer->name }}

<p>Por medio del presente, <b>{{ $sale->subscriber->name }}</b> le hace llegar la {{ ($sale->type=='C')?'cotización':'factura' }} solicitada la cual está adjunta a este correo.</p>

<ul>
	<li><strong>Folio:</strong> {{ $sale->folio }}</li>
	<li><strong>Fecha:</strong> {{ $sale->date->format('d/m/Y') }}</li>
	@if($sale->type=='C')
		<li><strong>Vencimiento:</strong> {{ ($sale->due_date)?$sale->due_date->format('d/m/Y'):'' }}</li>
	@endif
	<li><strong>Cliente:</strong> {{ $sale->customer->name }}</li>
	<li><strong>Contacto:</strong> {{ $sale->contact }}</li>
</ul>

@slot('footer')
        @component('mail::footer')
            <b>{{ $sale->subscriber->name }}</b><br>
			{{ $sale->subscriber->address }}, {{ $sale->subscriber->state->name }} {{ $sale->subscriber->city }}
        @endcomponent
@endslot
@endcomponent
