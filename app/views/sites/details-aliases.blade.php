
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

@section('aliases')

<div id="aliasContainer">
	<form id="aliasForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
	<fieldset>
	<ul class="forms" style="">
		<span id="aliasFormAlert"></span>
		
		<li><label for="server_name">Server Name</label></li>
		<li><input type="text" name="server_name" id="server_name" value="" placeholder="domain.tld:80" class="k-textbox"></li>
		
		<li><label for="aliases">Aliases</label></li>
		<li><textarea name="aliases" id="aliases" value="" placeholder="domain.tld:80" class="k-textarea"></textarea></li>
		
		<li>&nbsp;</li>
		<li>
			<input type="submit" id="formUpdateButton" class="k-button" value="Update">
		</li>
	</ul>
	</fieldset>
	</form>
</div>
	
@stop

@section('javascript')
@parent
<script type="text/javascript">
$(document).ready(function () {

	var alert = $("#alert").kendoNotification({
		appendTo: "#createFormAlert",
		autoHideAfter: 0,
		
	}).data("kendoNotification");
	
	
	$("#aliasForm").on("submit", function(event) {
		event.preventDefault();
	
		alert.hide();
		
		kendo.ui.progress($("#aliasContainer"), true);
		
		var url = "{{ URL::route('sites.update', ['id']) }}";
		url = url.replace('id', {{ $id }});
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"_method": "PUT", "aliases": $("#aliases").val(), "server_name": $("#server_name").val() },
			url: url,
			
			success: function(data, textStatus, xhr) {
				$('#aliasForm')[0].reset();
				alert.hide();
				kendo.ui.progress($("#aliasContainer"), false);
			},
			
			error: function(jqXHR, textStatus, errorThrown) {
				var message;
				if (jqXHR.status == 403) {
					var response = jqXHR.responseJSON;
					message = response.error.message;
				}
				else {
					message = 'An unknown error occurred';
				}
				
				kendo.ui.progress($("#aliasContainer"), false);
				alert.show(message, "error");
			}
		});
	});
	
});
</script>
@append
