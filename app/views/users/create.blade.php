
@section('style')
<style type="text/css">
.forms {
	list-style-type: none;
	padding-left: 50px;
	padding-top: 25px;
}
.k-textbox, .k-dropdown {
	width: 300px;
}
#aliases {
	width: 294px;
	height: 194px;
}
#createUser {
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
	<div id="createUser">
		
		<div>
			<form id="createForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
			<fieldset>
			<ul class="forms" style="">
				<span id="createFormHeader" style="font-size:18px;">Create</span><hr>
				<span id="createFormAlert"></span>
				
				<li><label for="username">Username</label></li>
				<li><input type="text" name="username" id="username" value="" class="k-textbox"></li>
				
				<li><label for="password">Password</label></li>
				<li><input type="password" name="password" id="password" value="" class="k-textbox"></li>
				<li><label for="password_confirmation">Password Confirmation</label></li>
				<li><input type="password" name="password_confirmation" id="password_confirmation" value="" class="k-textbox"></li>
				
				<li><label for="name">Name</label></li>
				<li><input type="text" name="name" id="name" value="" class="k-textbox"></li>
				
				<li><label for="role">Role</label></li>
				<li>{{ Form::select('role', $roles, null, array('id' => 'role')) }}</li>
				
				<li><label for="activate">Activate</label></li>
				<li>{{ Form::select('activate', array('No', 'Yes'), null, array('id' => 'activate')) }}</li>
				
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

	$("#role").kendoDropDownList({
		animation: {
			close: {
				effects: "fadeOut zoom:out",
				duration: 200
			},
			open: {
				effects: "fadeIn zoom:in",
				duration: 200
			}
		}
	});
	$("#activate").kendoDropDownList({
		animation: {
			close: {
				effects: "fadeOut zoom:out",
				duration: 200
			},
			open: {
				effects: "fadeIn zoom:in",
				duration: 200
			}
		}
	});

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
		
		kendo.ui.progress($("#createUser"), true);
		
		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {"username": $("#username").val(), "name": $("#name").val(), "password": $("#password").val(), "password_confirmation": $("#password_confirmation").val(), "activated": $("#activate").val(), "role": $("#role").val() },
			url: "{{ URL::route('users.store') }}",
			
			success: function(data, textStatus, xhr) {
				$('#createForm')[0].reset();
				//$("#createFormAlert").text("");
				//$("#createFormAlert").css({ display: "none" });
				alert.hide();
				$("#formCreateButton").prop("disabled", false);
				
				var grid = kendo.widgetInstance($('#grid'));
				
				grid.dataSource.read();
				grid.refresh();
				
				kendo.ui.progress($("#createUser"), false);
				kendo.fx($("#createUser")).zoom("out").play();
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
				
				kendo.ui.progress($("#createUser"), false);
				//$("#createFormAlert").text(message);
				//$("#createFormAlert").css({ display: "" });
				alert.show(message, "error");

			}
		});
	});
	
	
	$("#formCancelButton").bind("click", function(e) {
		kendo.fx($("#createUser")).zoom("out").play();
		
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