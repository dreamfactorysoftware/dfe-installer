@extends('dfe-common::layouts.common')

{{-- no spaces... it won't be trimmed --}}
{{-- @formatter:off --}}
@section('page-title'){{ "Login" }}@overwrite
{{-- @formatter:on --}}

@section('head-links')
    @parent
    <link href="/vendor/dfe-common/css/auth.css" rel="stylesheet">
@stop

@include('dfe-common::auth.branding')

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

                <form id="login-form" role="form" method="POST" action="/auth/login">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon bg_lg"><i class="fa fa-user"></i></span>

                            <input type="email"
                                class="form-control email required"
                                autofocus
                                name="email"
                                placeholder="email address"
                                value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon bg_ly"><i class="fa fa-lock"></i></span>

                            <input class="form-control password required" placeholder="password" name="password" type="password" />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember_token">
                                remember me
                            </label>
                        </div>
                    </div>

                    <label class="control-label sr-only" for="email">Email Address</label>
                    <label class="control-label sr-only" for="password">Password</label>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-sm-4">
								<span class="pull-left" style="margin-right: 10px;"><a href="/auth/register" class="btn btn-info">
                                        <i class="fa fa-fw fa-user"></i>Sign Up</a></span>
                            </div>

                            <div class="col-sm-4 pull-center">
                                <span><a href="/password/email" class="btn btn-warning"><i class="fa fa-fw fa-life-ring"></i>Lost password?</a></span>
                            </div>

                            <div class="col-sm-4">
                                <span class="pull-right"><button type="submit" class="btn btn-success"><i class="fa fa-fw fa-sign-in"></i>Login</button></span>
                            </div>
                        </div>
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
