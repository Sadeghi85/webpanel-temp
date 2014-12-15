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
Overview
@stop

@section('content')
@parent

@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {

});
</script>
@stop

@include('overviews.disk')