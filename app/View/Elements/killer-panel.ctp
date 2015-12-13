<?php $ck = $this->requestAction(__('/killers/currentKillers/%s', $user['id'])); ?>
<div class="small-12 medium-4 columns">
	<div>
		<a href="/" title="home">
			<img src="/img/gm_logo.png" alt="Goalmine Logo">
		</a>
	</div>
</div>

<div class="medium-8 columns">

<?php if (($user['games'] & 4) != 0): ?>
	<ul>
		<li><a href="/killer/4">Killer game 4</a></li>
	</ul>
<?php endif; ?>

</div>