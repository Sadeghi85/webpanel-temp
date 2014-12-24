

<style type="text/css">


</style>



<div id="" class="k-content">
	
		<div class="chart-wrapper">
			<div id="chart-memory"></div>
		</div>

	
</div>



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
		var chart = $("#chart-memory").kendoChart({
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


	}
	
	createChart();

});


</script>
