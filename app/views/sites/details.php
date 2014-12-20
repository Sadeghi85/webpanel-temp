@extends('layouts.splitter')

@section('title')
	@parent
@stop


@section('style')
@parent

<style type="text/css">

</style>
@stop

@section('header')
{{ '<h1>'.$tag.'</h1>' }}
@stop

@section('content')
@parent
	<div id="grid"></div>
	
@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {

	

$( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
console.log(jqxhr);
});
	
});
</script>
@stop
