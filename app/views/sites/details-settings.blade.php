
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
	<li></li>
	<li></li>
	<li></li>
</ul>
@stop

@section('javascript')
@parent
<script type="text/javascript">
$(document).ready(function () {

	var aliasesURL = "{{ URL::route('sites.get-details-settings-main', ['id']) }}";
	aliasesURL = aliasesURL.replace('id', {{ $id }});
		
	$("#settingsPanelbar").kendoPanelBar({
		animation: {
		   expand: {
				duration: 500,
				effects: "expandVertical fadeIn"
		   }
		},
		
		dataSource: [
			{
				text: "Aliases",
				expanded: true,
				contentUrl: aliasesURL
				
			},
			{
				text: "<b>Item 2</b>",
				encoded: false,                                 // Allows use of HTML for item text
				content: "text"                                 // content within an item
			},
			{
				text: "Item 3",
				// content URL to load within an item
				contentUrl: "http://demos.telerik.com/kendo-ui/content/web/panelbar/ajax/ajaxContent1.html"
			},
			{
				text: "Item 4",
				expanded: true,                                 // item is rendered expanded
				items: [{                                       // Sub item collection.
							text: "Sub Item 1"
						},
						{
							text: "Sub Item 2"
						}
					]
			},
			{
				text: "Item 5"
			}
		]
	   
	   
    });
	
});
</script>
@append
