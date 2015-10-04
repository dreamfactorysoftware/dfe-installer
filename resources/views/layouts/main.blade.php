<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ "DreamFactory Enterprise&trade; Installer" }}</title>
    <link rel="icon" type="image/png" href="/public/img/apple-touch-icon.png">
    <link href="/static/bootstrap-3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/font-awesome-4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
    <link href="/css/style.css" rel="stylesheet">
    <link rel="apple-touch-icon" href="/public/img/apple-touch-icon.png">
    <script type="text/javascript" src="/static/jquery-2.1.4/jquery.min.js"></script>

    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><img src="img/apple-touch-icon.png" alt="DreamFactory"><span>DreamFactory Enterprise&trade;
                    Installer</span></a>
        </div>
    </div><!-- /.container-fluid -->
</nav>

<div class="container-fluid">
    <div class="row">
        <div id="content" class="col-xs-12 col-sm-12 col-md-12 main">
            @yield('content')
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/bootstrap-3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
