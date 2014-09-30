<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="@lang('app.title')">
	<link rel="shortcut icon" href="/favicon.ico">
	
	<title>
	@section('title')
		{{ Lang::get('app.title') }}
	@show
	</title>
    
    <link href="/assets/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
	{{-- <link href="/assets/bootstrap/css/bootstrap-theme.css" rel="stylesheet" media="screen"> --}}
	@if (Config::get('app.locale') == 'fa')
	{{-- <link href="/assets/bootstrap/css/bootstrap-rtl.css" rel="stylesheet" media="screen"> --}}
	@endif
	<link href="/assets/_app/css/multi-select.css" rel="stylesheet" media="screen">
    <link href="/assets/jqwidgets/styles/jqx.base.css" rel="stylesheet" media="screen">
    <link href="/assets/jqwidgets/styles/jqx.bootstrap.css" rel="stylesheet" media="screen">
	<link href="/assets/_app/css/app.css" rel="stylesheet" media="screen">
	
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
@section('navbar')

@show
	
@section('container')
<div class="container" style="width:100%;">
	<!-- Content -->
	@yield('content')
</div>
@show

<script src="/assets/_app/js/jquery/jquery.min.js"></script>
<script src="/assets/_app/js/jquery/jquery.multi-select.js"></script>
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/jqwidgets/jqx-all.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
		// set jQWidgets Theme to "Bootstrap"
		$.jqx.theme = "bootstrap";
		
		@section('javascript')
		
		@show
	});
</script>

</body>
</html>