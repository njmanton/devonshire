<!-- app/View/elements/foot.ctp -->

<footer role="contentinfo" class="row">

	<div class="medium-3 small-6 columns">
		<h5>GAMES</h5>
		<ul class="no-bullet">
			<li><a href="/about/about/">GENERAL</a></li>
			<li><a href="/about/goalmine/">GOALMINE</a></li>
			<li><a href="/about/tipping/">TIPPING</a></li>
			<li><a href="/about/killer/">KILLER</a></li>
		</ul>
	</div>
	
	<div class="medium-3 small-6 columns">
		<h5>NAVIGATION</h5>
		<ul class="no-bullet">
			<li><a href="/">HOME</a></li>
			<li><a href="/weeks/">ALL FIXTURES</a></li>
			<li><a href="/league/">GOALMINE LEAGUE</a></li>
			<li><a href="/weeks/ytd/">TIPPING LEAGUE</a></li>
			<?php if ($user) { echo '<li><a href="/users/update">OPTIONS</a></li>'; } ?>
		</ul>
	</div>

	<div class="medium-3 small-6 columns">
		<h5>LISTS</h5>
		<ul class="no-bullet">
			<li><a href="/matches/">MATCHES</a></li>
			<li><a href="/teams/">TEAMS</a></li>
			<li><a href="/competitions/">COMPETITIONS</a></li>
		</ul>
	</div>

	<div class="medium-3 small-6 columns">
		<br />
		v6.1.0 (devonshire)
	</div>

</footer>



