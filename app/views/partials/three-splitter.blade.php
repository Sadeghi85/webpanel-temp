@extends('layouts.default')

@section('style')
@parent

<style type="text/css">
html,
body
{
    height:100%;
    margin:0;
    padding:0;
    overflow:hidden;
}

#mainContainer {
	width:100%;
	height:100%;
}

#mainRow {
	
	height:100%;
}

#horizontalSplitter
{
    height:100%;
	border: 0;
	width:100%;
}

#verticalSplitter
{
    border: 0;
	height:100%;
	width:100%;
}


</style>
@stop

@section('navbar')
	@include('partials.navbar')
@stop

@section('container')
<div class="container" id="mainContainer">
<div class="row" id="mainRow">


	<div id="verticalSplitter">
		
		<div style="height:100%;">
			<div id="horizontalSplitter">
				<div><p>Top Side, Left Pane Content</p></div>
				<div style="height:100%;">@yield('content')</div>
			</div>
		</div>
		
		<div style="height:100%;"><p>Bottom Side Pane Content</p></div>
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
			{ collapsible: false },
			{ collapsible: false, resizable: true, size: '20%', min: '100px' }
		]
	});

	$("#horizontalSplitter").kendoSplitter({
		panes: [
			{ collapsible: true, resizable: true, size: '15%' },
			{ collapsible: false }
		]
	});
					
	//$("#horizontalSplitter").kendoSplitter();
	//$("#verticalSplitter").kendoSplitter({ orientation: "vertical" });
	
	// $("#splitter").kendoSplitter({
		// panes: [
			// { collapsible: true, min: "100px", max: "300px" },
			// { collapsible: true }
		// ],
		// orientation: "vertical"
	// });
});
</script>
@stop