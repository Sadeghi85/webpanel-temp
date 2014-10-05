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
	
	<link href="/assets/jquery/jquery-ui/themes/smoothness/jquery-ui.min.css" rel="stylesheet" media="screen">
	<link href="/assets/jquery/jquery-ui/themes/smoothness/theme.css" rel="stylesheet" media="screen">

	{{-- <link href="/assets/_app/css/multi-select.css" rel="stylesheet" media="screen"> --}}
	
    <link href="/assets/jqwidgets/styles/jqx.base.css" rel="stylesheet" media="screen">
    <link href="/assets/jqwidgets/styles/jqx.web.css" rel="stylesheet" media="screen">
	
	<link href="/assets/_app/css/app.css" rel="stylesheet" media="screen">
	

<style type="text/css">

	
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

<script src="/assets/jquery/jquery.min.js"></script>
<script src="/assets/jquery/jquery-ui/jquery-ui.min.js"></script>

{{-- <script src="/assets/_app/js/jquery/jquery.multi-select.js"></script> --}}

<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/jqwidgets/jqx-all.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
		// set jQWidgets Theme to "Bootstrap"
		$.jqx.theme = "web";
		
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