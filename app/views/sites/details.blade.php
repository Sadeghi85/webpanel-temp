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
{{ $tag }}
@stop

@section('content')
@parent
	<span id="alert"></span>
	<div id="tabstrip">
		<ul>
			<li id="overview">Overview</li>
			<li id="settings">Settings</li>
			<li id="logs">Logs</li>
		</ul>
		
		<div id="overviewContent">Content 1</div>
		<div id="settingsContent">
			<ul id="settingsPanelbar">
				<li>Aliases
					@section('aliases')
						@include('sites.details-aliases')
					@show
				</li>
				<li>Item 2
					<ul>
						<li>Sub Item 1</li>
						<li>Sub Item 2</li>
						<li>Sub Item 3</li>
					</ul>
				</li>
			</ul>
		</div>
		<div id="logsContent">Content 3</div>
	</div>
	
@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {

	$("#tabstrip").kendoTabStrip({
		animation: {
			// fade-out current tab over 1000 milliseconds
			close: {
				duration: 500,
				effects: "fadeOut"
			},
		   // fade-in new tab over 500 milliseconds
		   open: {
			   duration: 500,
			   effects: "fadeIn"
		   }
		}
	});
	
	var tabToActivate = $("#overview");
    $("#tabstrip").kendoTabStrip().data("kendoTabStrip").activateTab(tabToActivate);
	
	
	$("#settingsPanelbar").kendoPanelBar({
        animation: {
            // fade-out closing items over 1000 milliseconds
            collapse: {
                //duration: 1000,
                //effects: "fadeOut"
            },
           // fade-in and expand opening items over 500 milliseconds
           expand: {
               duration: 500,
               effects: "expandVertical fadeIn"
           }
       }
    });
		
	
$( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
console.log(jqxhr);
});
	
});
</script>
@stop
