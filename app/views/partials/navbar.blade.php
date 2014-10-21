

<div id="nav-section" class="col-xs-12 column">
	<!--open nav column-->
	<div class="navbar-default">
		<button id="toggle-button" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>
	<h1 id="dash-logo" class="center-block">Web Panel</h1>
	<div class="collapse navbar-collapse" id="sidebar-nav" role="navigation">
		<ul class="nav">
			<li id="regionalSalesStatus" class="{{ Helpers::activateTabIfRouteIs('overviews.*') }}">
				<a href="{{ URL::route('overviews.index') }}">
					<span class="icon icon-chart-column"></span>Overview</a>
			</li>
			<li id="productsAndOrders" class="{{ Helpers::activateTabIfRouteIs('users.*') }}">
				<a href="{{ URL::route('users.index') }}">
					<span class="icon icon-star-empty"></span>Users</a>
			</li>
			<li id="teamEfficiency" class="{{ Helpers::activateTabIfRouteIs('roles.*') }}">
				<a href="{{ URL::route('roles.index') }}">
					<span class="icon icon-faves"></span>Roles</a>
			</li>
			<li id="about">
				<a href="{{ URL::route('auth.logout') }}">
					<span class="icon icon-info"></span>Logout</a>
			</li>
		</ul>
		<span id="rights">Copyright © 2002-2014 Telerik. All rights reserved.</span>
	</div>
</div>
