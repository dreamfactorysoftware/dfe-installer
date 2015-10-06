@extends('dfe-common::layouts.common')

{{-- no spaces... it won't be trimmed --}}
{{-- @formatter:off --}}
@section('page-title'){{ "Password Reset" }}@overwrite
{{-- @formatter:on --}}

@section('head-links')
	@parent
	<link href="/vendor/dfe-common/css/auth.css" rel="stylesheet">
@stop

@include('dfe-common::auth.branding')

@section('content')
	<div id="container-login" class="container-fluid">
		<div class="row">
			<div class="col-md-offset-4 col-md-4 col-md-offset-4">
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

				<div class="alert alert-warning" role="alert">
					<p>We're sorry you have lost your password. Please enter your registered email address below and we will send you reset instructions.</p>
				</div>

				<form id="login-form" role="form" method="POST" action="/password/email">
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

					<label class="control-label sr-only" for="password">Password</label>

					<div class="form-actions">
						<span class="pull-left"><a href="/auth/login"
												   class="btn btn-success"><i class="fa fa-fw fa-angle-double-left"></i>Back to Login</a></span> <span
							class="pull-right"><button
								class="btn btn-danger"
								type="submit"><i class="fa fa-fw fa-send"></i>Send Reset Link
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

