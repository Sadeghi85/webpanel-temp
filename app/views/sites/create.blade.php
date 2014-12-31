
@section('style')
<style type="text/css">
.forms {
	list-style-type: none;
	padding-left: 50px;
	padding-top: 25px;
}
.k-textbox {
	width: 300px;
}
#server_aliases {
	width: 294px;
	height: 194px;
}
#createSite {
	display:none;
	padding-bottom: 10px;
}
label {
	padding: 5px;
}
hr {
	margin-top: 10px;
	margin-bottom: 10px;
}
.k-notification {
	display: inline-block !important;
}
</style>
@append

@section('content')
	<span id="alert"></span>
	<div id="createSite">
		
		<div>
			<form id="createForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
			<fieldset>
			<ul class="forms" style="">
				<span id="createFormHeader" style="font-size:18px;">Create</span><hr>
				<span id="createFormAlert"></span>
				
				<li><label for="port">Server Port</label></li>
				<li><input type="text" name="server_port" id="server_port" value="" placeholder="80" class="k-textbox"></li>
				
				<li><label for="server_name">Server Name</label></li>
				<li><input type="text" name="server_name" id="server_name" value="" placeholder="domain.tld" class="k-textbox"></li>
				
				<li><label for="aliases">Server Aliases</label></li>
				<li><textarea name="server_aliases" id="server_aliases" value="" placeholder="domain.tld" class="k-textarea"></textarea></li>
				
				<li><label for="server_quota">Server Quota (MB)</label></li>
				<li><input type="text" name="server_quota" id="server_quota" value="" placeholder="1000" class="k-textbox"></li>
				
				<li>&nbsp;</li>
				<li>
					<input type="submit" id="formCreateButton" class="k-button" value="Create">
					<a id="formCancelButton" class="k-button" >Cancel</a>
				</li>
			</ul>
			</fieldset>
			</form>
        </div>
		
	</div>
	
@append

@section('javascript')

<script type="text/javascript">
$(document).ready(function () {

	var alert = $("#alert").kendoNotification({
		appendTo: "#createFormAlert",
		autoHideAfter: 0,
		
	}).data("kendoNotification");
	  
	//$("#createFormAlert").css({ display: "none" });

	/* Create Button */
	$("#createForm").on("submit", function(event) {
		event.preventDefault();
	
		$("#formCreateButton").prop("disabled", true);
		
		//$("#createFormAlert").text("");
		//$("#createFormAlert").css({ display: "none" });
		alert.hide();
		
		kendo.ui.progress($("#createSite"), true);
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"server_aliases": $("#server_aliases").val(), "server_name": $("#server_name").val(), "server_port": $("#server_port").val(), "server_quota": $("#server_quota").val() },
			url: "{{ URL::route('sites.store') }}",
			
			success: function(data, textStatus, xhr) {
				$('#createForm')[0].reset();
				//$("#createFormAlert").text("");
				//$("#createFormAlert").css({ display: "none" });
				alert.hide();
				$("#formCreateButton").prop("disabled", false);
				
				var grid = kendo.widgetInstance($('#grid'));
				
				grid.dataSource.read();
				grid.refresh();
				
				kendo.ui.progress($("#createSite"), false);
				kendo.fx($("#createSite")).zoom("out").play();
				kendo.fx($("#grid")).zoom("in").play();
			},
			
			error: function(jqXHR, textStatus, errorThrown) {
				$("#formCreateButton").prop("disabled", false);
				
				var message;
				if (jqXHR.status == 403) {
					var response = jqXHR.responseJSON;
					message = response.error.message;
					
				}
				else {
					message = 'An unknown error occurred';
				}
				
				kendo.ui.progress($("#createSite"), false);
				//$("#createFormAlert").text(message);
				//$("#createFormAlert").css({ display: "" });
				alert.show(message, "error");

			}
		});
	});
	
	
	$("#formCancelButton").bind("click", function(e) {
		kendo.fx($("#createSite")).zoom("out").play();
		
		$('#createForm')[0].reset();
		//$("#createFormAlert").text("");
		//$("#createFormAlert").css({ display: "none" });
		alert.hide();
		
		kendo.fx($("#grid")).zoom("in").play();
	});
	/* /Create Button */
	
	
	
});
</script>
@append