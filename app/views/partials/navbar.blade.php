
<!-- Static navbar -->
<div class="navbar navbar-default navbar-static-top" style="margin-bottom:0;" role="navigation">
  <div class="container">
	<div class="navbar-header">
	  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" >Web Panel</a>
	</div>
	<div class="navbar-collapse collapse">
	  <ul class="nav navbar-nav">
		<li class="{{ Helpers::activateTabIfRouteIs('overviews.*') }}"><a href="{{ route('overviews.index') }}">Overview</a></li>
		<li class="{{ Helpers::activateTabIfRouteIs('groups.*') }}"><a href="{{ route('groups.index') }}">Groups</a></li>
	  
	  </ul>
	  
	  <ul class="nav navbar-nav navbar-right">
		<li><a href="{{ route('auth.logout') }}">Logout</a></li>
	   
	  </ul>
	  
	</div><!--/.nav-collapse -->
  </div>
</div>
