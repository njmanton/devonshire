<!-- /app/View/Weeks/ytd.ctp -->
<?php $this->set('title_for_layout', 'Tipping League Table'); ?>
<?php $x = 0; ?>
<section>
	<header>
		<h2>Current Tipping league table</h2>
		<p>This table shows the overall league positions for the tipping competition. Note the total balances on users&rsquo; pages may show a different total due to the way unplayed games are counted, but this table always shows the offical league positions.</p>
	</header>

	<table class="table">
		<thead>
			<tr>
				<th>Rank</th>
				<th>Player</th>
				<th>Balance</th>
				<th>Last Week</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($ytd)) echo '<tr><td style="text-align: center" colspan="4">No data yet</td></td>'; ?>
		<?php foreach($ytd as $k=>$y): ?>
			<tr>
				 
	<?php
		$prev = (array_key_exists('prevrank', $y)) ? $y['prevrank'] : 1000;

		if (++$x < $prev) {
			$color = __('<span style="color: %s;">%s</span>', 'green', '&#x25B2;');
		} elseif ($x > $prev) {
			$color = __('<span style="color: %s;">%s</span>', 'red', '&#x25BC;');
		} else {
			$color = __('<span style="color: %s;">%s</span>', 'black', '&#x25B6;');
		}
	?>

				<td><?php echo $color; ?> <?php echo $x; ?></td>
				<td><a href="/users/view/<?php echo $k; ?>"><?php echo $y['name']; ?></a></td>
				<td><?php echo aus($y['balance']); ?></td>
				<td><?php if (array_key_exists('prevbalance', $y)) echo aus($y['prevbalance']) . ' (' . $y['prevrank'] . ')'; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

</section>