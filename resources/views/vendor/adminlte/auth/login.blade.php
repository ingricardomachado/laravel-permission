@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])


@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset("css/admin_custom.css") }}">
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )
@php( $password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? route($password_reset_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
    @php( $password_reset_url = $password_reset_url ? url($password_reset_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.login_message'))

@section('auth_body')
    <!-- show erros -->
    @if (count($errors) > 0)
      <div class="alert alert-danger alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        <i class="fa fa-exclamation-triangle"></i> <strong>Disculpe!</strong><br>
          @foreach ($errors->all() as $error)
            {!! $error !!}
          @endforeach
      </div>
    @endif
    <!-- /show erros -->
    
    <form action="{{ $login_url }}" id="form" method="post">
        {{ csrf_field() }}

        {{-- Email field --}}
        <div class="form-group mb-3">
            <input type="email" name="email" class="form-control"
                   value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus required>
        </div>

        {{-- Password field --}}
        <div class="form-group mb-3">
            <input type="password" name="password" class="form-control"
                   placeholder="{{ __('adminlte::adminlte.password') }}" required>
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                <div class="icheck-primary">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">{{ __('adminlte::adminlte.remember_me') }}</label>
                </div>
            </div>
            <div class="col-5">
                <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>

    </form>
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    @if($password_reset_url)
        <p class="my-0">
            <a href="{{ $password_reset_url }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif

    {{-- Register link --}}
    @if($register_url)
        <p class="my-0">
            <a href="{{ $register_url }}">
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif
@stop
<script src="{{ asset("vendor/jquery/jquery.min.js") }}"></script>
<script>
$(document).ready(function() {
                
    // Validation
    $("#form").validate({
        submitHandler: function(form) {
            $("#btn_submit").attr("disabled",true);
            form.submit();
        }        
    });

});    
</script>