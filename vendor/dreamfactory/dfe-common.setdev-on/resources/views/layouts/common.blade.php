<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>@yield('page-title', 'Welcome!') | DreamFactory Enterprise&trade;</title>
	@section('head-theme')
		<link href="/vendor/dfe-common/static/bootswatch-3.3.4/darkly.min.css" rel="stylesheet">
	@show
	<link href="/static/font-awesome-4.3.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="/css/style.css" rel="stylesheet">
	<!--[if lt IE 9]>
	<script src="//oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
	<script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->

	@section('head-links')
	@show

	@section('head-scripts')
	@show
</head>
<body class="@yield('body-class')">

<div id="page-content" class="container-fluid">
	@yield('content')
</div>

@section('before-body-scripts')
@show

<script src="/static/jquery-2.1.4/jquery.min.js"></script>
<script src="/static/bootstrap-3.3.4/js/bootstrap.min.js"></script>

@section('after-body-scripts')
@show
</body>
</html>
