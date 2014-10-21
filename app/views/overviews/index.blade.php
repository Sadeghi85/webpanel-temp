@extends('layouts.default')

@section('title')
	@lang('app.title')
@stop


@section('style')
@parent
<style type="text/css">

</style>
@stop

@section('container')
<a href="{{ URL::route('auth.logout') }}">logout</a>

@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {



});
</script>
@stop