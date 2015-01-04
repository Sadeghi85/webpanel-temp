
<style type="text/css">
.forms {
	list-style-type: none;
	padding-left: 50px;
	padding-top: 25px;
	padding-bottom: 25px;
}
.k-textbox {
	width: 90%;
}
#server_aliases {
	width: 88%;
	height: 134px;
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
		<form id="mainSettingsForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
		<fieldset>
		<div class="row">
			<div class="col-md-offset-3 col-md-4">
				<li><input type="submit" id="formUpdateButton" class="k-button" value="Update"></li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
			</div>
		</div>
		
		<div class="row">
		<div class="col-md-3">
			<ul class="" style="">
				<li><label for="server_port">Server Port</label></li>
				<li><input type="text" name="server_port" id="server_port" value="{{ $serverPort }}" placeholder="80" class="k-textbox"></li>
				
				<li><label for="server_name">Server Name</label></li>
				<li><input type="text" name="server_name" id="server_name" value="{{ $serverName }}" placeholder="domain.tld" class="k-textbox"></li>
				
				<li><label for="server_aliases">Server Aliases</label></li>
				<li><textarea name="server_aliases" id="server_aliases" placeholder="domain.tld" class="k-textarea">{{ $serverAliases }}</textarea></li>
				
				<li><label for="server_quota">Server Quota (MB)</label></li>
				<li><input type="text" name="server_quota" id="server_quota" value="{{ $serverQuota }}" placeholder="1000" class="k-textbox"></li>

			</ul>
		</div>
		
		<div class="col-md-2">
			<ul class="" style="">
				<li><label for="limit_rate">Limit Rate</label></li>
				<li><input type="text" name="limit_rate" id="limit_rate" value="" placeholder="25" class="k-textbox"></li>
				
				<li><label for="limit_conn">Limit Conn</label></li>
				<li><input type="text" name="limit_conn" id="limit_conn" value="" placeholder="100" class="k-textbox"></li>
				
				<li><label for="max_children">Max Children</label></li>
				<li><input type="text" name="max_children" id="max_children" value="" placeholder="2" class="k-textbox"></li>
				
				<li><label for="start_servers">Start Servers</label></li>
				<li><input type="text" name="start_servers" id="start_servers" value="" placeholder="1" class="k-textbox"></li>
				
				<li><label for="min_spare_servers">Min Spare Servers</label></li>
				<li><input type="text" name="min_spare_servers" id="min_spare_servers" value="" placeholder="1" class="k-textbox"></li>
				
				<li><label for="max_spare_servers">Max Spare Servers</label></li>
				<li><input type="text" name="max_spare_servers" id="max_spare_servers" value="" placeholder="1" class="k-textbox"></li>

			</ul>
		</div>
		
		<div class="col-md-3">
			<ul class="" style="">
				<li><label for="request_terminate_timeout">request_terminate_timeout</label></li>
				<li><input type="text" name="request_terminate_timeout" id="request_terminate_timeout" value="" placeholder="60" class="k-textbox"></li>
				
				<li><label for="request_slowlog_timeout">request_slowlog_timeout</label></li>
				<li><input type="text" name="request_slowlog_timeout" id="request_slowlog_timeout" value="" placeholder="5" class="k-textbox"></li>
				
				<li><label for="post_max_size">post_max_size (MB)</label></li>
				<li><input type="text" name="post_max_size" id="post_max_size" value="" placeholder="8" class="k-textbox"></li>
				
				<li><label for="upload_max_filesize">upload_max_filesize (MB)</label></li>
				<li><input type="text" name="upload_max_filesize" id="upload_max_filesize" value="" placeholder="2" class="k-textbox"></li>
				
				<li><label for="max_file_uploads">max_file_uploads</label></li>
				<li><input type="text" name="max_file_uploads" id="max_file_uploads" value="" placeholder="20" class="k-textbox"></li>
				
				<li><label for="error_reporting">error_reporting</label></li>
				<li><input type="text" name="error_reporting" id="error_reporting" value="" placeholder="E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT" class="k-textbox"></li>
				
				

			</ul>
		</div>
		
		<div class="col-md-2">
			<ul class="" style="">
				<li><label for="memory_limit">memory_limit</label></li>
				<li><input type="text" name="memory_limit" id="memory_limit" value="" placeholder="32" class="k-textbox"></li>
				
				<li><label for="max_execution_time">max_execution_time</label></li>
				<li><input type="text" name="max_execution_time" id="max_execution_time" value="" placeholder="30" class="k-textbox"></li>
				
				<li><label for="max_input_time">max_input_time</label></li>
				<li><input type="text" name="max_input_time" id="max_input_time" value="" placeholder="30" class="k-textbox"></li>
				
				<li><label for="default_socket_timeout">default_socket_timeout</label></li>
				<li><input type="text" name="default_socket_timeout" id="default_socket_timeout" value="" placeholder="30" class="k-textbox"></li>
				
				<li><label for="date_timezone">date_timezone</label></li>
				<li><input type="text" name="date_timezone" id="date_timezone" value="" placeholder="Asia/Tehran" class="k-textbox"></li>
				
				<li><label for="output_buffering">output_buffering</label></li>
				<li><input type="text" name="output_buffering" id="output_buffering" value="" placeholder="4096" class="k-textbox"></li>
				
			</ul>
		</div>
		</div>
		
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
	
	var updateMainSettingsURL = "{{ URL::route('sites.post-details-settings-main', ['id']) }}";
	updateMainSettingsURL = updateMainSettingsURL.replace('id', {{ $id }});
	
	/* Update Button */
	$("#mainSettingsForm").on("submit", function(event) {
		event.preventDefault();
	
		$("#formUpdateButton").prop("disabled", true);
		
		alert.hide();
		
		kendo.ui.progress($("#mainSettingsContainer"), true);
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"server_aliases": $("#server_aliases").val(), "server_name": $("#server_name").val(), "server_port": $("#server_port").val(), "server_quota": $("#server_quota").val() },
			url: updateMainSettingsURL,
			
			success: function(data, textStatus, xhr) {
				
				kendo.ui.progress($("#mainSettingsContainer"), false);
				alert.show('Main settings updated.', "success")
				
				$("#formUpdateButton").prop("disabled", false);
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
				
				$("#formUpdateButton").prop("disabled", false);
			}
		});
	});
	
	
	
	
});
</script>

