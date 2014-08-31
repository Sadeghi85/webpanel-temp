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

    <link href="{{ asset('/assets/extjs/resources/css/ext-all.css') }}" rel="stylesheet" media="screen">
    <link href="{{ asset('/assets/app.css') }}" rel="stylesheet" media="screen">

    <script src="{{ asset('/assets/extjs/ext-all-dev.js') }}"></script>
    <script src="{{ asset('/assets/extjs/locale/ext-lang-en.js') }}"></script>
	<script src="{{ asset('/assets/app.js') }}"></script>

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