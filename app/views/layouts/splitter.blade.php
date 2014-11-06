@extends('layouts.default')

@section('style')
@parent

<style type="text/css">



</style>
@stop



@section('container')
<div class="container-fluid">
<div class="row row-offcanvas row-offcanvas-left">

	@section('navbar')
		@include('partials.navbar')
	@show
	
	<div id="main-section" class="col-xs-12 column">
		<div id="main-section-header" class="row">
			<h2 id="team-efficiency" class="col-xs-3">
			@section('header')
				
			@show
			</h2>
			<div style="clear:both;"></div>
		</div>
		
		<div id="verticalSplitter" style="height:100%;border: 0;">
			<div id="topPane">@yield('content')</div>
			<div id="bottomPane"><p>Bottom Side Pane Content</p></div>
		</div>
		
	</div>

	
	
	
</div>
</div>
@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {

	var verticalSplitter = $("#verticalSplitter").kendoSplitter({
		orientation: "vertical",
		panes: [
			{ collapsible: false, scrollable: false },
			{ collapsible: true, resizable: true }
		]
	}).data("kendoSplitter");
	
	resizeSplitter = function() {
		$("#verticalSplitter").height($(window).height() - $("#main-section-header").height());
		verticalSplitter.size("#topPane", $("#verticalSplitter").height() - "100px");
		verticalSplitter.size("#bottomPane", "100px");
	};
	
	verticalSplitter.bind("contentLoad", resizeSplitter);
	
	$(window).resize(function() {
		resizeSplitter();
	});
	resizeSplitter();
	


	
});
</script>
@stop