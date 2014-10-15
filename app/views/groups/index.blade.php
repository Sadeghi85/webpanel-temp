@extends('layouts.splitter')

@section('title')
	@lang('groups/messages.title') :: @parent
@stop


@section('style')
@parent

<style type="text/css">

.toolbar {
	float: left;
}

.k-splitter .k-pane {
	overflow: auto !important;
}




</style>
@stop

@section('content')
@parent

	
	
	<div id="grid"></div>

	<script id="template" type="text/x-kendo-template">
		<div class="toolbar">
			<button id="createButton">Create</button>
			<button id="editButton">Edit</button>
			<button id="removeButton">Remove</button>
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

	<div id="createDialog"></div>
	<script id="createDialogTemplate" type="text/x-kendo-template">
		<div class="box-col">
		<form id="createForm" method="POST" action="" accept-charset="UTF-8" autocomplete="off">
		<fieldset>
        <ul class="forms" style="list-style-type: none;padding-left: 20px;padding-right: 20px;">
			<li><label class="alert alert-danger" id="createFormAlert"></label></li>
			
            <li><label for="createFormName">Name</label></li>
			<li><input type="text" name="createFormName" id="createFormName" value="" class="k-textbox" /></li>
			<li>&nbsp;</li>
            <li><label for="createFormComment">Comment</label></li>
			<li><input type="text" name="createFormComment" id="createFormComment" value="" class="k-textbox" /></li>
			<li>&nbsp;</li>
            <li><input type="submit" class="k-button" value="Create" /></li>
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

	var grid = $("#grid").kendoGrid({
		dataSource: {
			type: "json",
			transport: {
				read: {
						url: "{{ route('groups.index') }}",
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
		toolbar: kendo.template($("#template").html()),
		
		filterable: true,
		filterable: {
			mode: "menu"
		},
		sortable: true,
		columnMenu: true,
		pageable: true,
		pageable: {
			pageSizes: true,
			refresh: true
		},
		resizable: true,
		//height: "100%",
		scrollable: false,
		selectable: "row",
		columns: [{
				field:"name",
				title: "Name",
				width: "20%",
				
			},
			{
				field: "comment",
				title: "Comment",
			}
		]
	}).data("kendoGrid");
	
	var createButton = $("#createButton").kendoButton().data("kendoButton");
	var editButton = $("#editButton").kendoButton({ enable: false }).data("kendoButton");
	var removeButton = $("#removeButton").kendoButton({ enable: false }).data("kendoButton");
	
	var createDialogTemplate = kendo.template($("#createDialogTemplate").html());
	var createDialog = $("#createDialog").kendoWindow({
		actions: [ "close" ],
		visible: false,
		modal: true,
		title: "Create"
	}).data("kendoWindow");
	
	createDialog.bind("open", function() {


		$("#createForm").on("submit", function(event) {
			event.preventDefault();
		
			$("#createFormAlert").text("");
			$("#createFormAlert").css({ display: "none" });
					
			$.ajax(
			{
				type: "POST",
				cache: false,
				dataType: "json",
				data: {name: $("#createFormName").val(), comment: $("#createFormComment").val() },
				url: "{{ URL::route('groups.index') }}",
				
				success: function(data, textStatus, xhr) {
					grid.dataSource.read();
					grid.refresh();
					createDialog.close();
				},
				
				error: function(request, textStatus, errorThrown) {
					var message;
					if (request.status == 403) {
						var response = request.responseJSON;
						var errors = response.errors;
						
						$.each(errors, function(element, error) {
							//showError(element, error[0]);
							message = error[0];
							field = element;
						});
					}
					else {
						message = 'An unknown error occurred';
					}
					
					$("#createFormAlert").text(message);
					$("#createFormAlert").css({ display: "inherit" });
					
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
	
	var removeDialogTemplate = kendo.template($("#removeDialogTemplate").html());
	var removeDialog = $("#removeDialog").kendoWindow({
		actions: [ "close" ],
		visible: false,
		modal: true,
		title: "Confirm"
	}).data("kendoWindow");
	
	removeDialog.bind("open", function() {
		var removeDialogButtonYes = $("#removeDialogButtonYes").kendoButton().data("kendoButton");
		var removeDialogButtonNo = $("#removeDialogButtonNo").kendoButton().data("kendoButton");
		
		removeDialogButtonNo.bind("click", function() {
			removeDialog.close();
		});
		removeDialogButtonYes.bind("click", function() {
			var selectedRow = grid.select();
			selectedRow = grid.dataItem(selectedRow[0]);
		
			$.ajax(
			{
				type: "POST",
				cache: false,
				dataType: "json",
				data: {_method: "DELETE"},
				url: "{{ URL::route('groups.index') }}/"+selectedRow.id,
				
				success: function(data, textStatus, xhr) {
					grid.dataSource.read();
					grid.refresh();
					removeDialog.close();
				},
				
				error: function(request, textStatus, errorThrown) {
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
		removeDialog.content(removeDialogTemplate({ name: selectedRow.name }));
		removeDialog.center().open();
	});
	
	

	grid.bind("change", function(e) {
		editButton.enable(true);
		removeButton.enable(true);
    });
	
	grid.bind("dataBound", function(e) {
		editButton.enable(false);
		removeButton.enable(false);
	});
	
	
});
</script>
@stop