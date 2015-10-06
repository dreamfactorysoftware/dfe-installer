@extends('dfe-common::layouts.common')

{{-- no spaces... it won't be trimmed --}}
{{-- @formatter:off --}}
@section('page-title'){{ "Registration Closed" }}@overwrite
{{-- @formatter:on --}}

@section('head-links')
    @parent
    <link href="/vendor/dfe-common/css/auth.css" rel="stylesheet">
@stop

@include('dfe-common::auth.branding',['pageDisplayName'=> config('dfe.common.display-name').': Registration Closed'])

@section('content')
    <div id="container-login" class="container-fluid">
        <div class="row">
            <div class="col-sm-offset-3 col-sm-6 col-sm-offset-3">
                <div class="jumbotron">
                    <div class="container-logo" style="margin-bottom: 15px;">
                        <img src="/vendor/dfe-common/img/registration-closed.png">
                    </div>
                    <div class="pull-center">
                        <h3>This system is closed to new registrations</h3>

                        <p class="text-warning"
                            style="font-size: 15px; margin-top: 0; padding-top: 0">Please contact your system's administrator for more information.</p>

                        <p><a href="/auth/login"
                                class="btn btn-success"
                                role="button"><i class="fa fa-fw fa-angle-double-left" style="text-align: left;"></i>Back to Login</a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@stop

@section( 'after-body-scripts' )
    @parent
    <script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.13.1/jquery.validate.min.js"></script>
    <script src="/vendor/dfe-common/js/auth.validate.js"></script>
@stop
