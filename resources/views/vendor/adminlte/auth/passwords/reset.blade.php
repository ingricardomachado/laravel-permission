@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

<link href="{{ asset("css/admin_custom.css") }}" rel="stylesheet">

@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.password_reset_message'))

@section('auth_body')
    <form action="{{ $password_reset_url }}" id="form" method="post">
        {{ csrf_field() }}

        {{-- Token field --}}
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email field --}}
        <div class="form-group mb-3">
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus required>
            @if($errors->has('email'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                </div>
            @endif        
        </div>

        {{-- Password field --}}
        <small>De 6 a 15 caracteres y 1 número.</small>
        <div class="form-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="{{ __('adminlte::adminlte.password') }}" required>
            @if($errors->has('password'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('password') }}</strong>
                </div>
            @endif        
        </div>

        {{-- Password confirmation field --}}
        <div class="form-group mb-3">
            <input type="password" name="password_confirmation" class="form-control" placeholder="{{ trans('adminlte::adminlte.retype_password') }}" required>
            @if($errors->has('password_confirmation'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </div>
            @endif
        </div>

        {{-- Confirm password reset button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-sync-alt"></span>
            {{ __('adminlte::adminlte.reset_password') }}
        </button>

    </form>
@stop
<script src="{{ asset("vendor/jquery/jquery.min.js") }}"></script>
<script>
$(document).ready(function() {
           
    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please check your input."
    );
    
    $("#form").validate({            
        rules: {
            password:{
                required: true,
                minlength: 6,
                maxlength: 15,
                regex: /^(?:[0-9]+[a-z]|[a-z]+[0-9])[a-z0-9]*$/i
            },
        },
        messages: {
            password:{
                regex: 'La contraseña debe contener de 6 y 15 caracteres y al menos 1 número'
            },            
        },
        submitHandler: function(form) {
            form.submit();
        }        
    });

});
</script>
