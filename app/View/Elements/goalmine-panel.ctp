<div class="small-12 medium-4 columns">
	<div>
		<a href="/" title="home">
		<img src="/img/gm_logo.png" alt="Goalmine Logo">
	</a>
	</div>
</div>
<div class="medium-8 columns">
	<ul>
		<li>
			League <a href="/standings">Standings</a>
		</li>

		<?php foreach ($uw as $k=>$u): ?>
			<?php if ($u['goalmine']['matches'] > 0): ?>
				<li>
					<a href="/predictions/<?=$k; ?>">Week <?=$k; ?></a>
					<?php if ($user && ($user['games'] & 1) != 0) {
						$label = ($u['goalmine']['preds'] < 12) ? 'alert' : 'success';
						echo __('<span class="label %s">%s / %s</span>', $label, $u['goalmine']['preds'], $u['goalmine']['matches']);
					}	?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
		<li>
			The Goalmine <a title="View pot" href="/money/0">pot</a> currently stands at <?=sterling($this->requestAction('/ledgers/pot')); ?>
		</li>
	</ul>
</div>