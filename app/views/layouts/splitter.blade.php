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
			<h2 id="team-efficiency" class="col-xs-3">PRODUCTS & ORDERS</h2>
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

	$("#verticalSplitter").kendoSplitter({
		orientation: "vertical",
		panes: [
			{ collapsible: false, scrollable: false },
			{ collapsible: true, resizable: true }
		]
	});
	
	resizeSplitter = function() {
		$("#verticalSplitter").height($(window).height() - $("#main-section-header").height());
	
		var verticalSplitter = $("#verticalSplitter").data("kendoSplitter");
		
		
		//verticalSplitter.size("#topPane", $("#verticalSplitter").height() - "100px");
		verticalSplitter.size("#topPane", ".");
		verticalSplitter.size("#bottomPane", "100px");
		
	};
	
	$("#verticalSplitter").data("kendoSplitter").bind("contentLoad", resizeSplitter);
	
	$(window).resize(function() {
		resizeSplitter();
	});

	


	
});
</script>
@stop