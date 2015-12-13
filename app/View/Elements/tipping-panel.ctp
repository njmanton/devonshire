<div class="small-12 medium-4 columns">
	<div>
		<a href="/" title="home">
			<img src="/img/gm_logo.png" alt="Goalmine Logo">
		</a>
	</div>
</div>

<div class="medium-8 columns">
	<ul>
		<li>Tipping League <a href="/weeks/ytd">Standings</a></li>
		<?php foreach ($uw as $k=>$u): ?>
			<?php if ($u['tipping']['matches'] > 0): ?>
				<li>
					<a href="/bets/<?=$k; ?>">Week <?=$k; ?></a>
					<?php if ($user && ($user['games'] & 2) != 0) {
						$label = ($u['tipping']['preds'] < 3) ? 'alert' : 'success';
						echo __('<span class="label %s">%s bets</span>', $label, $u['tipping']['preds'], $u['tipping']['matches']);
					}	?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</div>
