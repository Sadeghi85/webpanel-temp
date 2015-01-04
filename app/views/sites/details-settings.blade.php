
@section('style')
@parent
<style type="text/css">
.forms {
	list-style-type: none;
	padding-left: 50px;
	padding-top: 25px;
	padding-bottom: 25px;
}
.k-textbox {
	width: 300px;
}
#aliases {
	width: 294px;
	height: 194px;
}
label {
	padding: 5px;
}
.k-notification {
	display: inline-block !important;
}
</style>
@append

@section('settings')
<ul id="settingsPanelbar">
	<li></li>
	<li></li>

</ul>
@stop

@section('javascript')
@parent
<script type="text/javascript">
$(document).ready(function () {

	var mainSettingsURL = "{{ URL::route('sites.get-details-settings-main', ['id']) }}";
	mainSettingsURL = mainSettingsURL.replace('id', {{ $id }});
		
	$("#settingsPanelbar").kendoPanelBar({
		animation: {
		   expand: {
				duration: 500,
				effects: "expandVertical fadeIn"
		   }
		},
		
		dataSource: [
			{
				text: "Main Settings",
				expanded: true,
				contentUrl: mainSettingsURL
				
			},
			{
				text: "Performance Settings",
				expanded: false,
				contentUrl: mainSettingsURL
				
			}
		]
	   
	   
    });
	
});
</script>
@append
