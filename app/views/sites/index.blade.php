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
.k-button {
	margin-right: 10px;
}
.k-grid {
	padding-right: 7px;

}
</style>
@stop

@section('header')
Sites
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
	
		columns: [{
				field:"tag",
				title: "Tag",
				width: "200px"
				
			},{
				field:"activated",
				title: "Activated",
				width: "200px"
				
			},
			{
				field: "alias",
				title: "Aliases",
				width: "auto",
				sortable: false,
				//filterable: false
			}
			//,{ hidden: false, menu:false, field: "id" },
		],
		dataSource: {
			type: "json",
			transport: {
				read: {
						url: "{{ route('sites.index') }}",
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
		sortable: {
			allowUnsort: false,
			//mode: "multiple",
		},
		columnMenu: false,
		pageable: true,
		pageable: {
			pageSizes: true,
			refresh: true
		},
		resizable: true,
		toolbar: kendo.template($("#template").html()),
		height: "99%",
		width: "100%",
		scrollable: true,
		selectable: "row",
		reorderable: true
		
	}).data("kendoGrid");
	
	var createButton = $("#createButton").kendoButton().data("kendoButton");
	var removeButton = $("#removeButton").kendoButton({ enable: false }).data("kendoButton");
	
	createButton.bind("click", function(e) {
		kendo.fx($("#grid")).zoom("out").play();
		kendo.fx($("#createSite")).zoom("in").play();
	});
	
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
			kendo.ui.progress($("#grid"), true);
			removeDialog.close();
			
			var grid = kendo.widgetInstance($('#grid'));
			var selectedRow = grid.select();
			selectedRow = grid.dataItem(selectedRow[0]);
			var url = "{{ URL::route('sites.destroy', ['id']) }}";
			url = url.replace('id', selectedRow.id);
		
			$.ajax(
			{
				type: "POST",
				cache: false,
				dataType: "json",
				data: {_method: "DELETE"},
				url: url,
				
				success: function(data, textStatus, xhr) {
					$("#removeButton").prop("disabled", false);
					kendo.ui.progress($("#grid"), false);
					grid.dataSource.read();
					grid.refresh();
				},
				
				error: function(request, textStatus, errorThrown) {
					$("#removeButton").prop("disabled", false);
					kendo.ui.progress($("#grid"), false);
					grid.dataSource.read();
					grid.refresh();
				}
			});
		});
	});
	
	removeButton.bind("click", function(e) {
		var selectedRow = grid.select();
		selectedRow = grid.dataItem(selectedRow[0]);
		removeDialog.content(removeDialogTemplate({ name: selectedRow.tag }));
		removeDialog.center().open();
	});
	/* /Remove Button */
	
	grid.bind("change", function(e) {
		removeButton.enable(true);
    });
	
	grid.bind("dataBound", function(e) {
		removeButton.enable(false);
		//resizeSplitter();
	});

$( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
console.log(jqxhr);
});
	
});
</script>
@stop

@include('sites.create')