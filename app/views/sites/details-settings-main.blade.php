
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


<div class="forms" id="mainSettingsContainer">
	<span id="alert"></span>
	
	<span id="mainSettingsAlert"></span>
	
	<div>
		<form id="portForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
		<fieldset>
		<ul class="" style="">
			<li><label for="port">Port</label></li>
			<li><input type="text" name="port" id="port" value="{{ $port }}" placeholder="80" class="k-textbox"><input type="submit" id="formUpdatePortButton" style="margin-left: 20px;" class="k-button" value="Update"></li>
		</ul>
		</fieldset>
		</form>
	</div>
	
	<div>
		<form id="serverNameForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
		<fieldset>
		<ul class="" style="">
			<li><label for="server_name">Server Name</label></li>
			<li><input type="text" name="server_name" id="server_name" value="{{ $serverName }}" placeholder="domain.tld" class="k-textbox"><input type="submit" style="margin-left: 20px;" id="formUpdateServerNameButton" class="k-button" value="Update"></li>
		</ul>
		</fieldset>
		</form>
	</div>
	
	<div>
		<form id="aliasesForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
		<fieldset>
		<ul class="" style="">
			<li><label for="aliases">Aliases</label></li>
			<li><textarea name="aliases" id="aliases" placeholder="domain.tld" class="k-textarea">{{ $aliases }}</textarea><input type="submit" style="margin-left: 20px; margin-bottom: 20px;" id="formUpdateAliasesButton" class="k-button" value="Update"></li>
		</ul>
		</fieldset>
		</form>
	</div>
</div>
	
<script type="text/javascript">
$(document).ready(function () {

	var alert = $("#alert").kendoNotification({
		appendTo: "#mainSettingsAlert",
		autoHideAfter: 0,
		
	}).data("kendoNotification");
	
	// port
	$("#portForm").on("submit", function(event) {
		event.preventDefault();

		alert.hide();
		
		kendo.ui.progress($("#mainSettingsContainer"), true);
		
		var url = "{{ URL::route('sites.post-details-settings-main-port', ['id']) }}";
		url = url.replace('id', {{ $id }});
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"port": $("#port").val() },
			url: url,
			
			success: function(data, textStatus, xhr) {
				//$('#portForm')[0].reset();
				//alert.hide();
				kendo.ui.progress($("#mainSettingsContainer"), false);
				alert.show('"Port" updated to "' + $("#port").val() + '".', "success")
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
				
				kendo.ui.progress($("#mainSettingsContainer"), false);
				alert.show(message, "error");
			}
		});
	});
	
	// server_name
	$("#serverNameForm").on("submit", function(event) {
		event.preventDefault();

		alert.hide();
		
		kendo.ui.progress($("#mainSettingsContainer"), true);
		
		var url = "{{ URL::route('sites.post-details-settings-main-servername', ['id']) }}";
		url = url.replace('id', {{ $id }});
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"server_name": $("#server_name").val() },
			url: url,
			
			success: function(data, textStatus, xhr) {
				//$('#portForm')[0].reset();
				//alert.hide();
				kendo.ui.progress($("#mainSettingsContainer"), false);
				alert.show('"Server Name" updated to "' + $("#server_name").val() + '".', "success")
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
				
				kendo.ui.progress($("#mainSettingsContainer"), false);
				alert.show(message, "error");
			}
		});
	});
	
	// aliases
	$("#aliasesForm").on("submit", function(event) {
		event.preventDefault();

		alert.hide();
		
		kendo.ui.progress($("#mainSettingsContainer"), true);
		
		var url = "{{ URL::route('sites.post-details-settings-main-aliases', ['id']) }}";
		url = url.replace('id', {{ $id }});
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"aliases": $("#aliases").val() },
			url: url,
			
			success: function(data, textStatus, xhr) {
				//$('#portForm')[0].reset();
				//alert.hide();
				kendo.ui.progress($("#mainSettingsContainer"), false);
				alert.show('"Aliases" updated.', "success")
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
				
				kendo.ui.progress($("#mainSettingsContainer"), false);
				alert.show(message, "error");
			}
		});
	});
	
	
});
</script>

