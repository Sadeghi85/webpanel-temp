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
		</div>
		<div id="logsContent">Content 3</div>
	</div>
	
@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {

	var getSettingsURL = "{{ URL::route('sites.get-details-settings', ['id']) }}";
	getSettingsURL = getSettingsURL.replace('id', {{ $id }});

	var ts = $("#tabstrip").kendoTabStrip({
		animation: {
			close: {
				duration: 500,
				effects: "fadeOut"
			},
		   open: {
			   duration: 500,
			   effects: "fadeIn"
		   }
		},
		contentUrls: [
            null,
            getSettingsURL,
			null,
        ]
	}).data('kendoTabStrip');
	
	setTimeout(function () {
            ts.reload($('#tabstrip #settings'));
        })
	
	
    ts.activateTab($('#overview'));
	
	
	
	$( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
	console.log(jqxhr);
	});
});
</script>
@stop
