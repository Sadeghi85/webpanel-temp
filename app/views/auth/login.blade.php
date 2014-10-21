@extends('layouts.default')

@section('title')
	@lang('auth/messages.login.title') :: @lang('app.title')
@stop


@section('style')
@parent
<style type="text/css">
#loginForm {
	width: 300px;
}

#username, #password, #loginButton {
	width: 260px;
}

.forms {
	list-style-type: none;
	padding-left: 20px;
	padding-right: 20px;
}

.box-col {
	padding-bottom: 10px;
	padding-top: 10px;
}
</style>
@stop

@section('container')

	<div id="loginDialog"></div>
	<script id="loginDialogTemplate" type="text/x-kendo-template">
		<div class="box-col">
		<form id="loginForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
		<fieldset>
        <ul class="forms">
			<li><label class="alert alert-danger" id="loginFormAlert"></label></li>
			
            <li><label for="username">Username</label></li>
			<li><input type="text" name="username" id="username" value="" class="k-textbox" tabindex="1"></li>
			<li>&nbsp;</li>
            <li><label for="password">Password</label></li>
			<li><input type="password" name="password" id="password" value="" class="k-textbox" tabindex="2"></li>
			<li>&nbsp;</li>
			<li><label for="remember">Remember</label></li>
			<li><input tabindex="4" type="checkbox" name="remember" id="remember" value="0" class="k-checkbox"></li>
			<li>&nbsp;</li>
            <li><input type="submit" id="loginButton" class="k-button" value="Login" tabindex="3"></li>
        </ul>
		</fieldset>
		</form>
        </div>
	</script>
@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {

	var loginDialogTemplate = kendo.template($("#loginDialogTemplate").html());
	var loginDialog = $("#loginDialog").kendoWindow({
		actions: [  ],
		visible: false,
		modal: true,
		resizable: false,
		draggable: false,
		title: "Login"
	}).data("kendoWindow");

	loginDialog.bind("open", function() {
		$("#loginForm").on("submit", function(event) {
			event.preventDefault();
			
			$("#loginButton").prop("disabled", true);
			
			if ($("#remember").is(':checked')) {
				$("#remember").val("1");
			} else {
				$("#remember").val("0");
			}
			
			$("#loginFormAlert").text("");
			$("#loginFormAlert").css({ display: "none" });
					
			$.ajax(
			{
				type: "POST",
				cache: false,
				dataType: "json",
				data: {username: $("#username").val(), password: $("#password").val(), remember: $("#remember").val()},
				url: "{{ URL::route('auth.login') }}",
				
				success: function(data, textStatus, xhr) {
					$("#loginButton").prop("disabled", false);
					
					window.location.replace(data.redirect);
				},
				
				error: function(jqXHR, textStatus, errorThrown) {
					$("#loginButton").prop("disabled", false);
					
	  
					var message;
					if (jqXHR.status == 403) {
						var response = jqXHR.responseJSON;
						message = response.error.message;
						
					}
					else {
						message = 'An unknown error occurred';
					}
					
					$("#loginFormAlert").text(message);
					$("#loginFormAlert").css({ display: "" });
					
				}
			});
		});
	});

	loginDialog.content(loginDialogTemplate({ }));
	$("#loginFormAlert").text("");
	$("#loginFormAlert").css({ display: "none" });
	loginDialog.center().open();

});
</script>
@stop