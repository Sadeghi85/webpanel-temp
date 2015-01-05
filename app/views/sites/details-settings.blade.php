

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
#formUpdateButton {
	width: 100%;
}
</style>


<div class="forms" id="settingsContainer">
	<span id="alert"></span>
	<span id="settingsAlert"></span>
	
	<div>
		<form id="settingsForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
		<fieldset>
		<div class="row">
			<div class="col-md-offset-3 col-md-4">
				<li><input type="submit" id="formUpdateButton" class="k-button" value="Update"></li>
				<li>&nbsp;</li>
				<li>&nbsp;</li>
			</div>
		</div>
		
		<div class="row">
		<div class="col-md-2">
			<ul class="" style="">
				<li><label for="server_port">Server Port</label></li>
				<li><input type="text" name="server_port" id="server_port" value="{{ $serverSettings['server_port'] }}" placeholder="80" class="k-textbox"></li>
				
				<li><label for="server_name">Server Name</label></li>
				<li><input type="text" name="server_name" id="server_name" value="{{ $serverSettings['server_name'] }}" placeholder="domain.tld" class="k-textbox"></li>
				
				<li><label for="server_aliases">Server Aliases</label></li>
				<li><textarea name="server_aliases" id="server_aliases" placeholder="domain.tld" class="k-textarea">{{ $serverSettings['server_aliases'] }}</textarea></li>
				
				<li><label for="server_quota">Server Quota (MB)</label></li>
				<li><input type="text" name="server_quota" id="server_quota" value="{{ $serverSettings['server_quota'] }}" placeholder="10" class="k-textbox"></li>

			</ul>
		</div>
		
		<div class="col-md-2">
			<ul class="" style="">
				<li><label for="limit_rate">limit_rate (KB/s)</label></li>
				<li><input type="text" name="limit_rate" id="limit_rate" value="{{ $serverSettings['limit_rate'] }}" placeholder="25" class="k-textbox"></li>
				
				<li><label for="limit_conn">limit_conn</label></li>
				<li><input type="text" name="limit_conn" id="limit_conn" value="{{ $serverSettings['limit_conn'] }}" placeholder="100" class="k-textbox"></li>
				
				<li><label for="max_children">max_children</label></li>
				<li><input type="text" name="max_children" id="max_children" value="{{ $serverSettings['max_children'] }}" placeholder="2" class="k-textbox"></li>
				
				<li><label for="start_servers">start_servers</label></li>
				<li><input type="text" name="start_servers" id="start_servers" value="{{ $serverSettings['start_servers'] }}" placeholder="1" class="k-textbox"></li>
				
				<li><label for="min_spare_servers">min_spare_servers</label></li>
				<li><input type="text" name="min_spare_servers" id="min_spare_servers" value="{{ $serverSettings['min_spare_servers'] }}" placeholder="1" class="k-textbox"></li>
				
				<li><label for="max_spare_servers">max_spare_servers</label></li>
				<li><input type="text" name="max_spare_servers" id="max_spare_servers" value="{{ $serverSettings['max_spare_servers'] }}" placeholder="1" class="k-textbox"></li>

			</ul>
		</div>
		
		<div class="col-md-3">
			<ul class="" style="">
				<li><label for="request_terminate_timeout">request_terminate_timeout (s)</label></li>
				<li><input type="text" name="request_terminate_timeout" id="request_terminate_timeout" value="{{ $serverSettings['request_terminate_timeout'] }}" placeholder="60" class="k-textbox"></li>
				
				<li><label for="request_slowlog_timeout">request_slowlog_timeout (s)</label></li>
				<li><input type="text" name="request_slowlog_timeout" id="request_slowlog_timeout" value="{{ $serverSettings['request_slowlog_timeout'] }}" placeholder="5" class="k-textbox"></li>
				
				<li><label for="post_max_size">post_max_size (MB)</label></li>
				<li><input type="text" name="post_max_size" id="post_max_size" value="{{ $serverSettings['post_max_size'] }}" placeholder="8" class="k-textbox"></li>
				
				<li><label for="upload_max_filesize">upload_max_filesize (MB)</label></li>
				<li><input type="text" name="upload_max_filesize" id="upload_max_filesize" value="{{ $serverSettings['upload_max_filesize'] }}" placeholder="2" class="k-textbox"></li>
				
				<li><label for="max_file_uploads">max_file_uploads</label></li>
				<li><input type="text" name="max_file_uploads" id="max_file_uploads" value="{{ $serverSettings['max_file_uploads'] }}" placeholder="20" class="k-textbox"></li>
				
				<li><label for="error_reporting">error_reporting</label></li>
				<li><input type="text" name="error_reporting" id="error_reporting" value="{{ $serverSettings['error_reporting'] }}" placeholder="E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT" class="k-textbox"></li>

			</ul>
		</div>
		
		<div class="col-md-3">
			<ul class="" style="">
				<li><label for="memory_limit">memory_limit (MB)</label></li>
				<li><input type="text" name="memory_limit" id="memory_limit" value="{{ $serverSettings['memory_limit'] }}" placeholder="32" class="k-textbox"></li>
				
				<li><label for="max_execution_time">max_execution_time (s)</label></li>
				<li><input type="text" name="max_execution_time" id="max_execution_time" value="{{ $serverSettings['max_execution_time'] }}" placeholder="30" class="k-textbox"></li>
				
				<li><label for="max_input_time">max_input_time (s)</label></li>
				<li><input type="text" name="max_input_time" id="max_input_time" value="{{ $serverSettings['max_input_time'] }}" placeholder="30" class="k-textbox"></li>
				
				<li><label for="default_socket_timeout">default_socket_timeout (s)</label></li>
				<li><input type="text" name="default_socket_timeout" id="default_socket_timeout" value="{{ $serverSettings['default_socket_timeout'] }}" placeholder="30" class="k-textbox"></li>
				
				<li><label for="date_timezone">date.timezone</label></li>
				<li><input type="text" name="date_timezone" id="date_timezone" value="{{ $serverSettings['date_timezone'] }}" placeholder="Asia/Tehran" class="k-textbox"></li>
				
				<li><label for="output_buffering">output_buffering (B)</label></li>
				<li><input type="text" name="output_buffering" id="output_buffering" value="{{ $serverSettings['output_buffering'] }}" placeholder="4096" class="k-textbox"></li>
				
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
		appendTo: "#settingsAlert",
		autoHideAfter: 0,
		
	}).data("kendoNotification");
	
	var updateSettingsURL = "{{ URL::route('sites.post-details-settings', ['id']) }}";
	updateSettingsURL = updateSettingsURL.replace('id', {{ $id }});
	
	/* Update Button */
	$("#settingsForm").on("submit", function(event) {
		event.preventDefault();
	
		$("#formUpdateButton").prop("disabled", true);
		
		alert.hide();
		
		kendo.ui.progress($("#settingsContainer"), true);
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"server_aliases": $("#server_aliases").val(), "server_name": $("#server_name").val(), "server_port": $("#server_port").val(), "server_quota": $("#server_quota").val(), "limit_rate": $("#limit_rate").val(), "limit_conn": $("#limit_conn").val(), "max_children": $("#max_children").val(), "start_servers": $("#start_servers").val(), "min_spare_servers": $("#min_spare_servers").val(), "max_spare_servers": $("#max_spare_servers").val(), "request_terminate_timeout": $("#request_terminate_timeout").val(), "request_slowlog_timeout": $("#request_slowlog_timeout").val(), "post_max_size": $("#post_max_size").val(), "upload_max_filesize": $("#upload_max_filesize").val(), "max_file_uploads": $("#max_file_uploads").val(), "memory_limit": $("#memory_limit").val(), "output_buffering": $("#output_buffering").val(), "default_socket_timeout": $("#default_socket_timeout").val(), "max_input_time": $("#max_input_time").val(), "max_execution_time": $("#max_execution_time").val(), "error_reporting": $("#error_reporting").val(), "date_timezone": $("#date_timezone").val() },
			url: updateSettingsURL,
			
			success: function(data, textStatus, xhr) {
				
				kendo.ui.progress($("#settingsContainer"), false);
				alert.show('Server settings updated.', "success")
				
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
				
				kendo.ui.progress($("#settingsContainer"), false);
				alert.show(message, "error");
				
				$("#formUpdateButton").prop("disabled", false);
			}
		});
	});

	
});
</script>

