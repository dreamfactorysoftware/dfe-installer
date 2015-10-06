@extends('dfe-common::layouts.common')
<?php
//  Just in case somehow registration page was up when this setting was off...
if ( !config( 'auth.open-registration', false ) )
{
    \Response::redirectToRoute( env( 'DFE_CLOSED_ROUTE', 'auth/login' ) );
}
?>
{{-- no spaces... it won't be trimmed --}}
{{-- @formatter:off --}}
@section('page-title'){{ "Register" }}@overwrite
{{-- @formatter:on --}}

@section('head-links')
    @parent
    <link href="/vendor/dfe-common/css/auth.css" rel="stylesheet">
@stop

@include('dfe-common::auth.branding',['pageDisplayName'=> config('dfe.common.display-name').': Register'])

@section('content')
    <div id="container-login" class="container-fluid">
        <div class="row">
            <div class="col-md-offset-4 col-md-4 col-md-offset-4 col-sm-offset-2 col-sm-6 col-sm-offset-2">
                <div class="container-logo">
                    @yield('auth.branding')
                </div>

                @if (count($errors) > 0)
                    <div class="alert alert-warning alert-dismissible alert-rounded fade in" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4>Rut-roh...</h4>

                        <ul style="list-style: none">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="form-register" role="form" method="POST" action="/auth/register">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <label for="email_addr_text" class="control-label">Email Address</label>
                        <input type="email"
                            class="form-control email required"
                            autofocus
                            name="email_addr_text"
                            id="email_addr_text"
                            value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="password_text" class="control-label">Password</label>
                        <input class="form-control password required" name="password_text" id="password_text" type="password" />
                    </div>

                    <div class="form-group">
                        <label for="password_text_confirmation" class="control-label">Confirm Password</label>
                        <input class="form-control password required" name="password_text_confirmation" id="password_text_confirmation" type="password" />
                    </div>

                    <div class="form-group">
                        <label for="first_name_text" class="control-label">First Name</label>
                        <input type="text"
                            class="form-control required"
                            name="first_name_text"
                            id="first_name_text"
                            value="{{ old('first_name_text') }}">
                    </div>

                    <div class="form-group">
                        <label for="last_name_text" class="control-label">Last Name</label>
                        <input type="text"
                            class="form-control required"
                            name="last_name_text"
                            id="last_name_text"
                            value="{{ old('last_name_text') }}">
                    </div>

                    <div class="form-group">
                        <label for="nickname_text" class="control-label">Nickname</label>
                        <input type="text"
                            class="form-control required"
                            name="nickname_text"
                            id="nickname_text"
                            value="{{ old('nickname_text') }}">
                    </div>

                    <div class="form-actions">
                        <span class="pull-left"><a href="/" class="btn btn-info"><i class="fa fa-fw fa-angle-double-left"></i>Cancel</a></span>

						<span class="pull-right"><button type="submit" class="btn btn-success">Register<i class="fa fa-fw fa-right fa-angle-double-right"></i>
                            </button></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section( 'after-body-scripts' )
    @parent
    <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
    <script src="/vendor/dfe-common/js/auth.validate.js"></script>
@stop
