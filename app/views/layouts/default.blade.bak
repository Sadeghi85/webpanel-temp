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
	<link href="{{-- /assets/bootstrap/css/bootstrap-theme.css --}}" rel="stylesheet" media="screen">
	@if (Config::get('app.locale') == 'fa')
	<link href="{{-- /assets/bootstrap/css/bootstrap-rtl.css --}}" rel="stylesheet" media="screen">
	@endif
	<link href="{{-- /assets/_app/css/multi-select.css --}}" rel="stylesheet" media="screen">
    <link href="/assets/jqwidgets/styles/jqx.base.css" rel="stylesheet" media="screen">
    <link href="/assets/jqwidgets/styles/jqx.bootstrap.css" rel="stylesheet" media="screen">
	
@section('style') 
   <style type="text/css">
		body, html {
            height: 100%;
            padding: 0px;
            margin: 0px;
            width: 100%;
            border: none;
            overflow: hidden;
        }
		
		.jqx-tree-item-arrow-collapse, .jqx-tree-item-arrow-expand {
			visibility: hidden;
		}
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
	<!-- Static navbar -->
    <div class="navbar navbar-default navbar-static-top" style="margin-bottom:0;" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Web Panel</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="#">Profile</a></li>
          
          </ul>
		  <!--
          <ul class="nav navbar-nav navbar-right">
            <li><a href="../navbar/">Default</a></li>
           
          </ul>
		  -->
        </div><!--/.nav-collapse -->
      </div>
    </div>
	
@section('container')
<div class="container" style="width:100%;">

<div class="row" style="">
	<div id="mainSplitter">
        <div class="container">
			<div class="row">
				<div id='jqxTree' style="padding: 0 14px 0 14px;">
					<ul>
						<li>&nbsp;</li>
						<li item-selected="true" style="background: #ffffff url('/assets/_app/img/switch.png') no-repeat 0px 0px; padding-left: 25px;"><a href="#">Home</a></li>
						<li style="background: #ffffff url('/assets/_app/img/switch.png') no-repeat 15px 0px; padding-left: 25px; margin-left:16px;"><a href="#">Solutions</a>
							<ul>
								<li style="background: #ffffff url('/assets/_app/img/switch.png') no-repeat 0px 0px; padding-left: 25px;"><a href="#">Education</a></li>
								<li>Financial services</li>
								<li>Government</li>
								<li>Manufacturing</li>
								<li>Solutions
									<ul>
										<li>Consumer photo and video</li>
										<li>Mobile</li>
										<li>Rich Internet applications</li>
										<li>Technical communication</li>
										<li>Training and eLearning</li>
										<li>Web conferencing</li>
									</ul>
								</li>
								<li>All industries and solutions</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
        </div>
        <div>
            <div id="rightSplitter">
                <div>
                    Top-Right Panel
                </div>
                <div>
                    Bottom-Right Panel
                </div>
            </div>
        </div>
    </div>
   
	<!-- Content -->
	@yield('content')
</div>
</div>
@show

	<script src="/assets/_app/js/jquery/jquery.min.js"></script>
	<script src="/assets/_app/js/jquery/jquery.multi-select.js"></script>
	<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/jqwidgets/jqx-all.js"></script>
    
@section('javascript')
	<script type="text/javascript">
		$(document).ready(function () {
			// set jQWidgets Theme to "Bootstrap"
            $.jqx.theme = "bootstrap";
			
            $('#mainSplitter').jqxSplitter({ width: '', height: $(window).height() - $('.navbar').height() - 4, panels: [{ size: '15%', min: '10%' }, { size: '85%', min: '80%' }] });
            $('#rightSplitter').jqxSplitter({ theme: 'bootstrap', width: '100%', height: '100%', orientation: 'horizontal', panels: [{ size: '60%', min: '55%', collapsible: false }, { collapsible: true }] });
			
			$('#jqxTree').jqxTree({ height: $(window).height() - $('.navbar').height() - 4, width: '', toggleMode: 'click' });
			
			$(window).resize(function() {
				$("#mainSplitter").jqxSplitter({ height: $(window).height() - $('.navbar').height() - 4 });
				$('#mainSplitter').jqxSplitter('refresh');
				$('#rightSplitter').jqxSplitter('refresh');
				$('#jqxTree').jqxTree({ height: $(window).height() - $('.navbar').height() - 4, width: '' });
			});
        });
	</script>
@show
</body>
</html>