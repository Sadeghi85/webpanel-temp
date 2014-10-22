@extends('layouts.splitter')

@section('title')
	@parent
@stop


@section('style')
@parent

<style type="text/css">
.toolbar {
	float: left;
	padding-left: 22.5px;
}
.k-grid-header-wrap {
    width: 102%;
}
.k-grid-footer-wrap {
    width: 102%;
}
.k-grid-content {
    overflow-y: auto    
}
</style>
@stop

@section('content')
@parent
	<div id="grid"></div>
	<script id="template" type="text/x-kendo-template">
		<div class="toolbar">
			<button id="createButton">Create</button>
			<button id="removeButton">Remove</button>
		</div>
	</script>
	<script id="detail-template" type="text/x-kendo-template"></script>
	
	<div id="createDialog"></div>
	<script id="createDialogTemplate" type="text/x-kendo-template">
		<div class="box-col">
		<form id="createForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
		<fieldset>
        <ul class="forms" style="list-style-type: none;padding-left: 20px;padding-right: 20px;">
			<li><label class="alert alert-danger" id="createFormAlert"></label></li>
			
            <li><label for="username">Username</label></li>
			<li><input type="text" name="username" id="username" value="" class="k-textbox" tabindex="1"></li>
			<li>&nbsp;</li>
            <li><label for="password">Password</label></li>
			<li><input type="password" name="password" id="password" value="" class="k-textbox" tabindex="2"></li>
			<li>&nbsp;</li>
			<li><label for="confirm-password">Confirm Password</label></li>
			<li><input type="password" name="confirm-password" id="confirm-password" value="" class="k-textbox" tabindex="3"></li>
			<li>&nbsp;</li>
			<li><label for="full-name">Full Name</label></li>
			<li><input type="text" name="full-name" id="full-name" value="" class="k-textbox" tabindex="4"></li>
			<li>&nbsp;</li>
			
            <li><input type="submit" id="createButton" class="k-button" value="Create" tabindex="5"></li>
        </ul>
		</fieldset>
		</form>
        </div>
	</script>
	
	<div id="removeDialog"></div>
	<script id="removeDialogTemplate" type="text/x-kendo-template">
			<div class="alert alert-danger" role="alert">Are you sure you want to remove entry "<span>#= name #</span>"?</div>
			<div style="text-align: center;">
				<button class="k-button" style="width: 50px;margin-right: 10px;" id="removeDialogButtonYes">Yes</button>
				<button style="width: 50px;margin-leftt: 10px;" id="removeDialogButtonNo" class="k-button" >No</button>
			</div>
	</script>
	
	
@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {

	var grid = $("#grid").kendoGrid({
		dataSource: {
			type: "json",
			transport: {
				read: {
						url: "{{ route('users.index') }}",
						dataType: "json"
				}
			},
			schema: {
				data: "data", total: "total"
			},
			pageSize: 10,
			serverPaging: true,
			serverFiltering: true,
			serverSorting: true
		},

		detailTemplate: kendo.template($("#detail-template").html()),
		filterable: true,
		filterable: {
			mode: "menu"
		},
		sortable: true,
		columnMenu: false,
		pageable: true,
		pageable: {
			pageSizes: true,
			refresh: true
		},
		resizable: true,
		toolbar: kendo.template($("#template").html()),
		height: "100%",
		width: "100%",
		scrollable: true,
		selectable: "row",
		reorderable: true,
		columns: [{
				field:"username",
				title: "Username",
				width: "200px"
				
			},{
				field:"name",
				title: "Full Name",
				width: "200px"
				
			},
			{
				field: "roles",
				title: "Roles",
				width: "auto",
				sortable: false,
				filterable: false
			}
			//,{ hidden: false, menu:false, field: "id" },
		]
	}).data("kendoGrid");
	
	
	var createButton = $("#createButton").kendoButton().data("kendoButton");
	var removeButton = $("#removeButton").kendoButton({ enable: false }).data("kendoButton");
	
	/* Create Button */
	var createDialogTemplate = kendo.template($("#createDialogTemplate").html());
	var createDialog = $("#createDialog").kendoWindow({
		actions: [ "close" ],
		visible: false,
		modal: true,
		resizable: false,
		draggable: false,
		title: "Create"
	}).data("kendoWindow");
	
	createDialog.bind("open", function() {
		$("#createForm").on("submit", function(event) {
			event.preventDefault();
		
			$("#createButton").prop("disabled", true);
			
			$("#createFormAlert").text("");
			$("#createFormAlert").css({ display: "none" });
			
			$.ajax(
			{
				type: "POST",
				cache: false,
				dataType: "json",
				data: {"username": $("#username").val(), "password": $("#password").val(), "confirm-password": $("#confirm-password").val(), "full-name": $("#full-name").val() },
				url: "{{ URL::route('users.store') }}",
				
				success: function(data, textStatus, xhr) {
					$("#createButton").prop("disabled", false);
					grid.dataSource.read();
					grid.refresh();
					createDialog.close();
				},
				
				error: function(jqXHR, textStatus, errorThrown) {
					$("#createButton").prop("disabled", false);
					
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
					
					grid.dataSource.read();
					grid.refresh();
					//createDialog.close();
				}
			});
		});
	});
	
	createButton.bind("click", function(e) {
		createDialog.content(createDialogTemplate({ }));
		
		$("#createFormAlert").text("");
		$("#createFormAlert").css({ display: "none" });
		
		createDialog.center().open();
	});
	/* /Create Button */
	
	/* Remove Button */
	var removeDialogTemplate = kendo.template($("#removeDialogTemplate").html());
	var removeDialog = $("#removeDialog").kendoWindow({
		actions: [ "close" ],
		visible: false,
		modal: true,
		resizable: false,
		draggable: false,
		title: "Confirm"
	}).data("kendoWindow");
	
	removeDialog.bind("open", function() {
		var removeDialogButtonYes = $("#removeDialogButtonYes").kendoButton().data("kendoButton");
		var removeDialogButtonNo = $("#removeDialogButtonNo").kendoButton().data("kendoButton");
		
		removeDialogButtonNo.bind("click", function() {
			removeDialog.close();
		});
		removeDialogButtonYes.bind("click", function() {
			$("#removeButton").prop("disabled", true);
			var selectedRow = grid.select();
			selectedRow = grid.dataItem(selectedRow[0]);
		
			$.ajax(
			{
				type: "POST",
				cache: false,
				dataType: "json",
				data: {_method: "DELETE"},
				url: "{{ URL::route('users.destroy') }}/"+selectedRow.id,
				
				success: function(data, textStatus, xhr) {
					$("#removeButton").prop("disabled", false);
					grid.dataSource.read();
					grid.refresh();
					removeDialog.close();
				},
				
				error: function(request, textStatus, errorThrown) {
					$("#removeButton").prop("disabled", false);
					grid.dataSource.read();
					grid.refresh();
					removeDialog.close();
				}
			});
		});
	});
	
	removeButton.bind("click", function(e) {
		var selectedRow = grid.select();
		selectedRow = grid.dataItem(selectedRow[0]);
		removeDialog.content(removeDialogTemplate({ name: selectedRow.username }));
		removeDialog.center().open();
	});
	/* /Remove Button */
	
	grid.bind("change", function(e) {
		removeButton.enable(true);
    });
	
	grid.bind("dataBound", function(e) {
		removeButton.enable(false);
		resizeSplitter();
	});

$( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
console.log(jqxhr);
});
	
});
</script>
@stop