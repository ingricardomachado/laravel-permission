@component('mail::layout')
@slot('header')
    @component('mail::header', ['url' => config('app.url')])
        {{ $purchase->subscriber->name }}
    @endcomponent
@endslot

# Estimado cliente {{ $purchase->supplier->name }}

<p>Por medio del presente, <b>{{ $purchase->subscriber->name }}</b> le hace llegar la {{ ($purchase->type=='O')?'orden de compra':'compra' }} solicitada la cual está adjunta a este correo.</p>

<ul>
	<li><strong>Folio:</strong> {{ $purchase->folio }}</li>
	<li><strong>Fecha:</strong> {{ $purchase->date->format('d/m/Y') }}</li>
	@if($purchase->type=='O')
		<li><strong>Vencimiento:</strong> {{ ($purchase->due_date)?$purchase->due_date->format('d/m/Y'):'' }}</li>
	@endif
	<li><strong>Proveedor:</strong> {{ $purchase->supplier->name }}</li>
	<li><strong>Contacto:</strong> {{ $purchase->contact }}</li>
</ul>

@slot('footer')
        @component('mail::footer')
            <b>{{ $purchase->subscriber->name }}</b><br>
			{{ $purchase->subscriber->address }}, {{ $purchase->subscriber->state->name }} {{ $purchase->subscriber->city }}
        @endcomponent
@endslot
@endcomponent
