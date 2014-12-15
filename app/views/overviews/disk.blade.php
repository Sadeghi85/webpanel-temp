
@section('style')
<style type="text/css">

</style>
@append

@section('content')
<div id="" class="k-content">
	<div id="window-disk">
		<div class="chart-wrapper">
			<div id="chart-disk"></div>
		</div>
	</div>
	
</div>
@append

@section('javascript')

<script type="text/javascript">
$(document).ready(function () {
	
	var data = [
		{
			"source": "Hydro",
			"percentage": 22,
			"explode": true
		},
		{
			"source": "Solar",
			"percentage": 2
		},
		{
			"source": "Nuclear",
			"percentage": 49
		},
		{
			"source": "Wind",
			"percentage": 27
		}
	];

	function createChart() {
		var chart = $("#chart-disk").kendoChart({
			title: {
				text: "Break-up of Spain Electricity Production for 2008"
			},
			legend: {
				position: "bottom"
			},
			dataSource: {
				data: data
			},
			series: [{
				type: "pie",
				field: "percentage",
				categoryField: "source",
				explodeField: "explode"
			}],
			seriesColors: ["#42a7ff", "#666666", "#999999", "#cccccc"],
			tooltip: {
				visible: true,
				template: "${ category } - ${ value }%"
			},
			seriesDefaults: {
				labels: {
					visible: true,
					background: "transparent",
					template: "#= category #: #= value#%"
				}
			},
			//chartArea: {
			//width: 300,
			//height: 300
			//},
		}).data("kendoChart");

		var window = $("#window-disk").data("kendoWindow");
		window.bind("resize", function(e) {
			chart.resize();
		});
	}

	var window = $('#window-disk').kendoWindow({
		width: "500px",
		title: "About Alvar Aalto",
		actions: [
		"Pin",
		"Minimize",
		"Maximize",
		"Close"
		],

		activate: createChart,
		visible: false
	}).data("kendoWindow");
	
	window.dragging._draggable.bind("drag", function (e) {
		var wnd = $("#window-disk").data("kendoWindow");
		var position = wnd.wrapper.position();

		var offset = $('#topPane').offset();
		var minT = offset.top;
		var minL = offset.left;
		
		var maxT = offset.top + $('#topPane').height() - $("#window-disk").height() - 50;
		var maxL = offset.left + $('#topPane').width() - $("#window-disk").width() - 20;

		if (position.left < minL) {
			coordinates = { left: minL };
			$(wnd.wrapper).css(coordinates);
		}

		if (position.top < minT) {
			coordinates = { top: minT };
			$(wnd.wrapper).css(coordinates);
		}

		if (position.left > maxL) {
			coordinates = { left: maxL };
			$(wnd.wrapper).css(coordinates);
		}

		if (position.top > maxT) {
			coordinates = { top: maxT };
			$(wnd.wrapper).css(coordinates);
		}
	});
	
	window.open();
});


</script>
@append