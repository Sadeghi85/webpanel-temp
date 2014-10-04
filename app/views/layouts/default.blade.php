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
		@lang('app.title')
	@show
	</title>
    
    <link href="/assets/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
	<link href="/assets/bootstrap/css/bootstrap-theme.css" rel="stylesheet" media="screen">
	@if (Config::get('app.locale') == 'fa')
	{{-- <link href="/assets/bootstrap/css/bootstrap-rtl.css" rel="stylesheet" media="screen"> --}}
	@endif
	<link href="/assets/_app/css/multi-select.css" rel="stylesheet" media="screen">
    <link href="/assets/jqwidgets/styles/jqx.base.css" rel="stylesheet" media="screen">
    <link href="/assets/jqwidgets/styles/jqx.energyblue.css" rel="stylesheet" media="screen">
	<link href="/assets/_app/css/app.css" rel="stylesheet" media="screen">
	

<style type="text/css">
.navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus {
	background-image: -webkit-linear-gradient(top,#ebebeb 0%,#f3f3f3 100%);
	background-image: -o-linear-gradient(top,#ebebeb 0%,#f3f3f3 100%);
	background-image: -webkit-gradient(linear,left top,left bottom,from(#ebebeb),to(#f3f3f3));
	background-image: linear-gradient(to bottom,#ebebeb 0%,#f3f3f3 100%);
	filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffebebeb', endColorstr='#fff3f3f3', GradientType=0);
	background-repeat: repeat-x;
	-webkit-box-shadow: inset 0 3px 9px rgba(0,0,0,.075);
	box-shadow: inset 0 3px 9px rgba(0,0,0,.075);
	background-color: #eee;
	}
	
	.jqx-grid-cell-sort {
		background-color: #fff;
	}
	
	@section('style') 
	
	@show
</style>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="{{ asset('/assets/_app/js/html5shiv.min.js') }}"></script>
      <script src="{{ asset('/assets/_app/js/respond.min.js') }}"></script>
    <![endif]-->
</head>
<body>

@include('partials.navbar')
	
@section('container')
<div class="container" style="width:100%;">
	<div class="row" style="">
		<div id="mainSplitter">
			<div style="overflow: auto;">
				
				<!-- Content -->
				@yield('content')
				
			</div>
			<div style="overflow: auto;">
				<iframe src="" style="width:100%;height:100%;"></iframe>
			</div>
		</div>
	   
	</div>
</div>
@show

<script src="/assets/_app/js/jquery/jquery.min.js"></script>
<script src="/assets/_app/js/jquery/jquery.multi-select.js"></script>
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/jqwidgets/jqx-all.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
		// set jQWidgets Theme to "Bootstrap"
		$.jqx.theme = "energyblue";
		
		$('#mainSplitter').jqxSplitter({ width: '', height: $(window).height() - $('.navbar').height() - 4, orientation: 'horizontal', panels: [{ size: '70%', min: '50%', collapsible: false }, { size: '30%', min: '10%', collapsible: false }] });
		
		
		
		
		$(window).resize(function() {
			$("#mainSplitter").jqxSplitter({ height: $(window).height() - $('.navbar').height() - 4 });
			$('#mainSplitter').jqxSplitter('refresh');
		});
		
		@section('javascript')
		
		@show
		
		
	});
</script>

</body>
</html>