

<div id="nav-section" class="col-xs-12 column">
                <!--open nav column-->
                <div class="navbar-default">
                    <button id="toggle-button" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <h1 id="dash-logo" class="center-block">Northwind&#183;Dash</h1>
                <div class="collapse navbar-collapse" id="sidebar-nav" role="navigation">
                    <ul class="nav">
                        <li id="regionalSalesStatus">
                            <a href="index.html">
                                <span class="icon icon-chart-column"></span>Regional Sales Status</a>
                        </li>
                        <li id="productsAndOrders">
                            <a href="products-orders.html">
                                <span class="icon icon-star-empty"></span>Products &amp; Orders</a>
                        </li>
                        <li id="teamEfficiency">
                            <a href="team-efficiency.html">
                                <span class="icon icon-faves"></span>Team Efficiency</a>
                        </li>
                        <li id="about">
                            <a href="about.html">
                                <span class="icon icon-info"></span>About</a>
                        </li>
                    </ul>
                    <span id="rights">Copyright © 2002-2014 Telerik. All rights reserved.</span>
                </div>
            </div>



<!-- Static navbar 
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
	  
	</div>
  </div>
</div>
-->