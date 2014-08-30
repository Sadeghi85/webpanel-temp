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

    <link href="{{ asset('/assets/css/ext/resources/css/ext-all.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet" media="screen">

  <script type="text/javascript">
//<![CDATA[
var BASE_URL = '';
//]]>
</script>

    <script src="{{ asset('/assets/js/ext/ext-dev.js') }}"></script>
    <script src="{{ asset('/assets/js/ext/locale/ext-lang-en.js') }}"></script>
	<script src="{{ asset('/assets/js/app.js') }}"></script>

@section('style') 
   <style type="text/css">
   
   </style>
@show

</head>

<body>

@section('container')
	<!-- Content -->
	@yield('content')
@show

@section('javascript')

	<script type="text/javascript">

	</script>
@show

</body>
</html>