
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
}
</style>
@append

@section('content')

	<div id="createSite">
		<div class="box-col">
			<form id="createForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
			<fieldset>
			<ul class="forms" style="">
				<li><span style="font-size:18px;">Create</style><hr></li>
				
				<li><label class="alert alert-danger" id="createFormAlert"></label></li>

				<li><label for="server-name">Server Name</label></li>
				<li><input type="text" name="server-name" id="server-name" value="" placeholder="domain.tld:80" class="k-textbox"></li>
				<li>&nbsp;</li>
				<li><label for="aliases">Aliases</label></li>
				<li><textarea name="aliases" id="aliases" value="" placeholder="domain.tld:80" class="k-textarea"></textarea></li>
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
	$("#createFormAlert").css({ display: "none" });

	/* Create Button */
	$("#createForm").on("submit", function(event) {
		event.preventDefault();
	
		$("#formCreateButton").prop("disabled", true);
		
		$("#createFormAlert").text("");
		$("#createFormAlert").css({ display: "none" });
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"aliases": $("#aliases").val(), "server-name": $("#server-name").val() },
			url: "{{ URL::route('sites.store') }}",
			
			success: function(data, textStatus, xhr) {
				$('#createForm')[0].reset();
				$("#createFormAlert").text("");
				$("#createFormAlert").css({ display: "none" });
				$("#formCreateButton").prop("disabled", false);
				
				var grid = kendo.widgetInstance($('#grid'));
				
				grid.dataSource.read();
				grid.refresh();
				
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
				
				$("#createFormAlert").text(message);
				$("#createFormAlert").css({ display: "" });
				
				//grid.dataSource.read();
				//grid.refresh();
				//
			}
		});
	});
	
	
	$("#formCancelButton").bind("click", function(e) {
		kendo.fx($("#createSite")).zoom("out").play();
		
		$('#createForm')[0].reset();
		$("#createFormAlert").text("");
		$("#createFormAlert").css({ display: "none" });
		
		kendo.fx($("#grid")).zoom("in").play();
	});
	/* /Create Button */
	
	
	
});
</script>
@append