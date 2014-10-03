@extends('layouts.default')

@section('title')
	@lang('groups/messages.title') :: @parent
@stop


@section('style')
@parent

@stop

@section('content')


	<div class="col-md-offset-1 col-md-10" style="margin-top:10px;">
	
		<div id="jqxgrid" ></div>
	
	</div>

@stop

@section('javascript')
	
	
	var source =
	{
		datatype: "json",
		datafields: [
			{ name: 'name' },
			{ name: 'comment' },
			//{ name: 'productname' },
			//{ name: 'quantity', type: 'int' },
			//{ name: 'price', type: 'float' },
			//{ name: 'total', type: 'float' }
		],
		cache: false,
		url: '{{ route('groups.index') }}',
		root: 'rows',
		sort: function () {
			// update the grid and send a request to the server.
			$("#jqxgrid").jqxGrid('updatebounddata', 'sort');
		},
		beforeprocessing: function (data) {
			source.totalrecords = data[0].totalrecords;
		}
	};

	
	var dataAdapter = new $.jqx.dataAdapter(source, {
		loadComplete: function (data) { },
		loadError: function (xhr, status, error) { }    
	});
	
	
	
	$("#jqxgrid").jqxGrid(
	{
		width: '100%',
		pageable: true,
		autoheight: true,
		autorowheight: true,
		altrows: true,
		enablebrowserselection: true,
		sortable: true,
		sorttogglestates: 1,
		virtualmode: true,
		source: dataAdapter,
		ready: function () {
			$("#jqxgrid").jqxGrid('setcolumnproperty', 'comment', 'width', '80%');
		},
		rendergridrows: function (params) {
			return params.data;
		},
		columns: [
			{ text: 'Name', datafield: 'name', width: '20%' },
			{ text: 'Comment', datafield: 'comment', width: '80%' },
		]
	});
	
	
		
	$(window).resize(function() {
		//$("#mainSplitter").jqxSplitter({ height: $(window).height() - $('.navbar').height() - 4 });
		$('#jqxgrid').jqxGrid('refresh');
	});
@stop