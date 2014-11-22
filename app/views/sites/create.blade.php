
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
#aliases {
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
				
				<li><label for="server-name">Server Name</label></li>
				<li><input type="text" name="server-name" id="server-name" value="" placeholder="domain.tld:80" class="k-textbox"></li>
				
				<li><label for="aliases">Aliases</label></li>
				<li><textarea name="aliases" id="aliases" value="" placeholder="domain.tld:80" class="k-textarea"></textarea></li>
				
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
			data: {"aliases": $("#aliases").val(), "server-name": $("#server-name").val() },
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