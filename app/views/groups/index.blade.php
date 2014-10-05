@extends('layouts.default')

@section('title')
	@lang('groups/messages.title') :: @parent
@stop


@section('style')
@parent

@stop

@section('content')


	<div class="col-md-offset-1 col-md-10" style="margin-top:40px; margin-bottom: 40px;">
	
		<div id="jqxgrid" style="min-width: 400px;"></div>
	
	</div>
	
	<div id='windowCreate' style="display: none;">
		<div>Create new group</div>
		<div>
			<div id="errorMessages"></div>
			<form id="formCreate" method="POST" action="" accept-charset="UTF-8" class="form-signin" autocomplete="off">
				<input type="text" name="name" value="{{ Input::old('name') }}" class="form-control" placeholder="Name" required autofocus>
				<input type="text" name="comment" value="{{ Input::old('comment') }}" class="form-control" placeholder="Comment" autofocus>
				<input class="btn btn-lg btn-primary btn-block" type="submit" value="Create">
			</form>
		</div>
	</div>
	
	<div id="alert" title="Alert" style="display: none;">
		
	</div>

@stop

@section('javascript')
	
	
	$("#formCreate").submit(function(event) {
		/* stop form from submitting normally */
		event.preventDefault();

		
		
		
		
		/* get some values from elements on the page: */
		var name = $(this).find('input[name="name"]').val();
		var comment = $(this).find('input[name="comment"]').val();

		$.ajax(
		{
			type: "POST",
			cache: false,
			dataType: "json",
			data: {name: name, comment: comment},
			url: "{{ URL::route('groups.store') }}",
			
			success: function(data, textStatus, xhr) {
				
				$(this).find('input[name="name"]').val("");
				$(this).find('input[name="comment"]').val("");
				
				$('#jqxgrid').jqxGrid('updatebounddata');
				
				$('#windowCreate').jqxWindow('close');
			},
			
			error: function(request, textStatus, errorThrown) {
				
				var message = "<ul>";
				if (request.status == 403)
				{
					var response = request.responseJSON;
					var errors = response.errors;
					
					$.each(errors, function(element, error) {
						
						message += "<li>" + error[0] + "</li>";
					});
				}
				else
				{
					message = "<li>An unknown error occurred</li>";
				}
				message += "</ul>";
				
				$('#alert').append(message);
				$( "#alert" ).dialog({
					modal: true,
					// buttons: {
						// Ok: function() {
						// $( this ).dialog( "close" );
						// }
					// }
				});
				
			}
		});

	});
	
	
	
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
		id: 'id',
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
		//altrows: true,
		//enabletooltips: true,
		editable: false,
		columnsresize: true,
		enablebrowserselection: true,
		sortable: true,
		sorttogglestates: 1,
		virtualmode: true,
		source: dataAdapter,
		showtoolbar: true,
		rendertoolbar: function (toolbar) {
			var container = $("<div id='' style='height:100%;'></div>");
			
			var buttonCreate = $("<input type='button' style='margin-left: 10px;position: relative;top: 15%;height: 70%;text-align: center;' value='Create' id='jqxbuttonCreate' />");
			var buttonEdit = $("<input type='button' style='margin-left: 10px;position: relative;top: 15%;height: 70%;text-align: center;' value='Edit' id='jqxbuttonEdit' />");
			var buttonRemove = $("<input type='button' style='margin-left: 10px;position: relative;top: 15%;height: 70%;text-align: center;' value='Remove' id='jqxbuttonRemove' />");
			
			toolbar.append(container);
			
			container.append(buttonCreate);
			container.append(buttonEdit);
			container.append(buttonRemove);
			
			$("#jqxbuttonCreate").jqxButton();
			$("#jqxbuttonEdit").jqxButton({ disabled: true});
			$("#jqxbuttonRemove").jqxButton({ disabled: true});
			
			$("#windowCreate").jqxWindow({ height:300, width: 600, autoOpen: false, isModal: true });
			
			$('#jqxbuttonCreate').on('click', function () {
				//$('#windowCreate').jqxWindow('open');
				
				$( "#alert" ).dialog({
					modal: true,
					buttons: {
						Ok: function() {
						$( this ).dialog( "close" );
						}
					}
				});
			});
		},
		ready: function () {
			
			
		},
		rendergridrows: function (params) {
			return params.data;
		},
		columns: [
			{ text: 'Name', datafield: 'name', minwidth: 100, width: '20%' },
			{ text: 'Comment', datafield: 'comment', minwidth: 100, resizable: false, width: 'auto' },
		]
	});
	
	$("#jqxgrid").on('bindingcomplete', function () {
		$("#jqxgrid").jqxGrid('setcolumnproperty', 'name', 'width', '20%');
		$("#jqxgrid").jqxGrid('setcolumnproperty', 'comment', 'width', 'auto');
	});

	
	$('#jqxgrid').on('rowselect', function (event) {
		$('#jqxbuttonEdit').jqxButton({disabled: false });
		$('#jqxbuttonRemove').jqxButton({disabled: false });
	});
	
	
	$('#jqxgrid').on('pagechanged', function (event) {
		$('#jqxbuttonEdit').jqxButton({disabled: true });
		$('#jqxbuttonRemove').jqxButton({disabled: true });
		
	});
	
	
		
	$(window).resize(function() {
		$('#jqxgrid').jqxGrid('render');
	});
@stop