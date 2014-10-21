@extends('layouts.splitter')

@section('title')
	@parent
@stop


@section('style')
@parent

<style type="text/css">

.k-grid-header-wrap {
    width: 102%;
}

.k-grid-footer-wrap {
    width: 102%;
}

.k-grid-content{
    overflow-y: auto    
}


</style>
@stop

@section('content')
@parent

	
	
	<div id="grid"></div>

	
	<script id="detail-template" type="text/x-kendo-template">
		
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
						url: "{{ route('roles.index') }}",
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
		columnMenu: true,
		pageable: true,
		pageable: {
			pageSizes: true,
			refresh: true
		},
		resizable: true,
		
		height: "100%",
		width: "100%",
		scrollable: true,
		selectable: "row",
		reorderable: true,
		columns: [{
				field:"name",
				title: "Name",
				width: "200px"
				
			},
			{
				field: "permissions",
				title: "Permissions",
				width: "auto"
			}
			//,{ hidden: false, menu:false, field: "id" },
		]
	}).data("kendoGrid");
	
	grid.bind("dataBound", function(e) {

		resizeSplitter();
	});

$( document ).ajaxError(function( event, jqxhr, settings, thrownError ) {
console.log(jqxhr);
});
	
});
</script>
@stop