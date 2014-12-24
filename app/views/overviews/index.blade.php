@extends('layouts.splitter')

@section('title')
	@parent
@stop


@section('style')
@parent
<style type="text/css">

</style>
@stop

@section('header')
Overview
@stop

@section('content')
@parent

<div class="row">

		<ul id="panelbarDisk"></ul>

		<ul id="panelbarMemory"></ul>

</div>

@stop

@section('javascript')
@parent

<script type="text/javascript">
$(document).ready(function () {
	$("#panelbarDisk").kendoPanelBar({
      dataSource: [
          {
              text: "Disk",
			  expanded: true,
              contentUrl: "{{ route('overviews.disk') }}"
          },
      ]
  });
  
  $("#panelbarMemory").kendoPanelBar({
      dataSource: [
          {
              text: "Memory",
			  expanded: true,
              contentUrl: "{{ route('overviews.memory') }}"
          },
      ]
  });
});
</script>
@stop

