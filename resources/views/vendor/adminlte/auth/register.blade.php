@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

<link href="{{ asset("css/admin_custom.css") }}" rel="stylesheet">

@section('plugins.Select2', true)

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
@endif

@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
    <form action="{{ $register_url }}" method="post" id="form">
        {{ csrf_field() }}

        <div class="form-group mb-3">
            <input type="text" name="name" id="name" class="form-control"
                   value="" maxlength="100" placeholder="{{ __('adminlte::adminlte.name') }}" required autofocus>
        </div>

        <div class="form-group mb-3">
            <input type="text" name="first_name" class="form-control"
                   value="" maxlength="100" placeholder="{{ __('adminlte::adminlte.first_name') }}" required autofocus>
        </div>
        
        <div class="form-group mb-3">
            <input type="text" name="last_name" class="form-control"
                   value="" maxlength="100" placeholder="{{ __('adminlte::adminlte.last_name') }}" required autofocus>
        </div>

        <div class="form-group mb-3">  
          {{ Form::select('country', $countries, null, ['id'=>'country', 'class'=>'select2 form-control form-control-sm', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
        </div>
        
        <div class="form-group mb-3">
            <input type="text" name="cell" id="cell" value="" oninput="this.value=this.value.replace(/[^0-9]/g,'');" class="form-control" placeholder="{{ __('adminlte::adminlte.cell') }}" minlength="10" maxlength="10" required/>
        </div>

        <div class="form-group mb-3">
            <input type="email" name="email" id="email" class="form-control"
                   value="" placeholder="{{ __('adminlte::adminlte.email') }}" required>
            <span id="msj_email" style="color:#cc5965"></span>
        </div>

        <small>De 6 a 15 caracteres y 1 número.</small>
        <div class="form-group mb-3">
            <input type="password" name="password"
                   class="form-control"
                   placeholder="{{ __('adminlte::adminlte.password') }}" required>
        </div>

        {{-- Register button --}}
        <button type="submit" id="btn_submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}" disabled>
            <span class="fas fa-user-plus"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>

    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $login_url }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>
@stop
<script src="{{ asset("vendor/jquery/jquery.min.js") }}"></script>
<script>
$(document).ready(function() {
        
    $("#country").select2({
        language: "es",
        placeholder: "{{ __('adminlte::adminlte.country') }}",
        minimumResultsForSearch: 10,
        allowClear: false,
        width: '100%'
    });    

    $('#email').blur(function(event) {
       verify_email($(this).val());
    });

    $('#email').focus(function(event) {
       $('#msj_email').html('');
    });

    function verify_email(email){
      $.ajax({
        url: `{{URL::to("verify_email")}}`,
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}", 
          email:email
        },
      })
      .done(function(response) {
        if(response.exists){
            $('#msj_email').html('Correo ya existe, intente con otro.');
            $('#btn_submit').attr('disabled',true);
        }else{
            $('#btn_submit').attr('disabled',false);
        }
      })
    }  
    
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
