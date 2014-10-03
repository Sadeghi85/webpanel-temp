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
		@lang('auth/messages.login.title') :: @lang('app.title')
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
body {
  background: #fff !important;
}
.form-signin
{
max-width: 330px;
padding: 15px;
margin: 0 auto;
}
.form-signin .form-signin-heading, .form-signin .checkbox
{
margin-bottom: 10px;
}
.form-signin .checkbox
{
font-weight: normal;
}
.form-signin .form-control
{
position: relative;
font-size: 16px;
height: auto;
padding: 10px;
-webkit-box-sizing: border-box;
-moz-box-sizing: border-box;
box-sizing: border-box;
}
.form-signin .form-control:focus
{
z-index: 2;
}
.form-signin input[type="text"]
{
margin-bottom: -1px;
border-bottom-left-radius: 0;
border-bottom-right-radius: 0;
}
.form-signin input[type="password"]
{
margin-bottom: 10px;
border-top-left-radius: 0;
border-top-right-radius: 0;
}
.account-wall
{
margin-top: 80px;
margin-bottom: 80px;
padding: 40px 0px 20px 0px;
background-color: #f7f7f7;
-moz-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
-webkit-box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.3);
}
.login-title
{
color: #555;
font-size: 18px;
font-weight: 400;
display: block;
}
.profile-img
{
width: 96px;
height: 96px;
margin: 0 auto 10px;
display: block;
-moz-border-radius: 50%;
-webkit-border-radius: 50%;
border-radius: 50%;
}
.need-help
{
margin-top: 10px;
}

.checkbox {
	margin-left: 20px;
}
</style>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="{{ asset('/assets/_app/js/html5shiv.min.js') }}"></script>
      <script src="{{ asset('/assets/_app/js/respond.min.js') }}"></script>
    <![endif]-->
</head>
<body>

<div class="container">	
<div class="row">
<div class="col-sm-6 col-md-4 col-md-offset-4">
<div class="account-wall">
<img class="profile-img" src="/assets/_app/img/login.png" alt="">
<form method="POST" action="{{ URL::route('auth.login') }}" accept-charset="UTF-8" class="form-signin" autocomplete="off">
<input type="text" name="username" value="{{ Input::old('username') }}" class="form-control" placeholder="{{ Lang::get('auth/messages.login.username') }}" required autofocus>
<input type="password" name="password" class="form-control" placeholder="{{ Lang::get('auth/messages.login.password') }}" required>
<label class="checkbox">
<input name="remember-me" type="checkbox" value="remember-me"> @lang('auth/messages.login.remember-me')
</label>
<input class="btn btn-lg btn-primary btn-block" type="submit" value="{{ Lang::get('auth/messages.login.login') }}">
</form>
</div>
</div>
</div>

<div id='errors' style="display: none;">
	<div>We encountered the following errors</div>
	<div>
		<ul style="padding-top: 20px">
		@foreach($errors->all() as $message)
			<li class="help-block">{{ $message }}</li>
		@endforeach
		</ul>
	</div>
</div>
</div>

<script src="/assets/_app/js/jquery/jquery.min.js"></script>
<script src="/assets/bootstrap/js/bootstrap.min.js"></script>
<script src="/assets/jqwidgets/jqx-all.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
		// set jQWidgets Theme to "Bootstrap"
		$.jqx.theme = "energyblue";
		
		$("#errors").jqxWindow({ height:300, width: 600, autoOpen: false, isModal: true });
	
		@if($errors->has())
		
			$('#errors').jqxWindow('open');
		@endif
	});
</script>

</body>
</html>