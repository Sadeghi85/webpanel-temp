<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="@lang('app.title')">
	<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
	
	<title>
	@section('title')
		{{ Lang::get('app.title') }}
	@show
	</title>
    
    <link href="{{ asset('/assets/bootstrap/css/bootstrap.css') }}" rel="stylesheet" media="screen">
	<link href="{{ asset('/assets/bootstrap/css/bootstrap-theme.css') }}" rel="stylesheet" media="screen">
	@if (Config::get('app.locale') == 'fa')
	<link href="{{ asset('/assets/bootstrap/css/bootstrap-rtl.css') }}" rel="stylesheet" media="screen">
	@endif
	<link href="{{ asset('/assets/_app/css/multi-select.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('/assets/jqwidgets/styles/jqx.base.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('/assets/jqwidgets/styles/jqx.bootstrap.css') }}" rel="stylesheet" media="screen">
	
@section('style') 
   <style type="text/css">
   
   </style>
@show

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="{{ asset('/assets/_app/js/html5shiv.min.js') }}"></script>
      <script src="{{ asset('/assets/_app/js/respond.min.js') }}"></script>
    <![endif]-->
</head>
<body>
<h1>Hello, world!</h1>

@section('container')
	<!-- Content -->
	@yield('content')
@show

	<script src="{{ asset('/assets/_app/js/jquery/jquery.min.js') }}"></script>
	<script src="{{ asset('/assets/_app/js/jquery/jquery.multi-select.js') }}"></script>
	<script src="{{ asset('/assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/assets/jqwidgets/jqxcore.js') }}"></script>
    
@section('javascript')
	<script type="text/javascript">
		$(document).ready(function () {
            // your JavaScript code here.
        });
	</script>
@show
</body>
</html>