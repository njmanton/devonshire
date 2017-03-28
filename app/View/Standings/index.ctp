<!-- /app/View/Leagues/index.php -->
<?php 
	$this->set('title_for_layout', 'Overall League'); 
	$arrows = [
		'up' => ['colour' => 'green','symbol' => '25B2'],
		'down' => ['colour' => 'red', 'symbol' => '25BC'],
		'right' => ['colour' => 'black', 'symbol' => '25B6']
	];
	$prevrank = 0;
?>

<section class="row">
	<header>
		<h2>Overall Goalmine League</h2>
	</header>
	
	<p>This table shows the overall league positions in this season of Goalmine. Only the top <strong>30</strong> scores in the season count towards the total.</p>
	<p>The final column shows the lowest score achieved (or 30th lowest score). Once a player has played 30 weeks this is the score at risk and will be dropped in favour of any better score.</p>
	<table class="table">
		<thead>
			<tr>
				<th>Rank (prev)</th>
				<th>Played</th>
				<th>Name</th>
				<th>Points</th>
				<th>Lowest</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($league)): ?>
			<tr><td style="text-align: center;" colspan="5">No Data Yet</td></tr>
		<?php endif; ?>
		<?php foreach ($league as $k=>$l): ?>
			<tr>
				<td>
					<?php
						$equal = '';
						if ($l['rank'] < $l['oldrank']) {
							$span = '<span style="color: ' . $arrows['up']['colour'] . ';">&#x' . $arrows['up']['symbol'] . ';</span>';
						} elseif ($l['rank'] > $l['oldrank']) {
							$span = '<span style="color: ' . $arrows['down']['colour'] . ';">&#x' . $arrows['down']['symbol'] . ';</span>';
						} else {
							$span = '<span style="color: ' . $arrows['right']['colour'] . ';">&#x' . $arrows['right']['symbol'] . ';</span>';
						}
						if ($l['rank']==$prevrank) {
							$equal = '=';
						}
						$prevrank = $l['rank'];
						echo $span . ' ' . $l['rank'] . $equal . ' (' . $l['oldrank'] . ')'; ?>
				</td>
				<td><?=$l['weeks']; ?></td>
				<td><a href="/users/view/<?php echo $k; ?>"><?php echo $l['user'] ?></a></td>
				<td><?=$l['points']; ?></td>
				<td><?php echo (isset($l['lowest'])) ? $l['lowest'] : '-'; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</section>

