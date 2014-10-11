@extends('partials.three-splitter')

@section('title')
	@lang('groups/messages.title') :: @parent
@stop


@section('style')
@parent

<style type="text/css">

.toolbar {
	float: left;
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
		sortable: true,
		//pageable: true,
		pageable: {
			pageSizes: true,
			refresh: true
		},
		columns: [{
				field:"name",
				title: "Name"
			},
			{
				field: "comment",
				title: "Comment"
			}
		]
	});
	
	var createButton = grid.find("#createButton").kendoButton();
	var createButton = grid.find("#editButton").kendoButton({
                        enable: false
                    });
	var createButton = grid.find("#removeButton").kendoButton({
                        enable: false
                    });
	
});
</script>
@stop