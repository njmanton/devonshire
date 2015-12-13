<?php $this->set('title_for_layout', $team); ?>
<section class="columns small-12">
	<header>
		<h2><?php echo $team; ?></h2>
	</header>
	<table>
		<caption>Summary</caption>
		<thead>
			<tr>
				<th>P</th>
				<th>W</th>
				<th>D</th>
				<th>L</th>
				<th>GF</th>
				<th>GA</th>
				<th>GD</th>
				<th>PTS</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?=$summary['P']; ?></td>
				<td><?=$summary['W']; ?></td>
				<td><?=$summary['D']; ?></td>
				<td><?=$summary['L']; ?></td>
				<td><?=$summary['GF']; ?></td>
				<td><?=$summary['GA']; ?></td>
				<td><?php if ($summary['GD'] > 0) { echo '+'; } echo $summary['GD']; ?></td>
				<td><?=$summary['PTS']; ?></td>
			</tr>
		</tbody>

	</table>
	<table class="f32">
		<thead>
			<tr>
				<th>id</th>
				<th>Date</th>
				<th style="width: 20em;">Competition</th>
				<th>Opponent</th>
				<th>Result</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($list)) echo '<td style="text-align: center;" colspan="6">No matches played yet</td>'; ?>
		<?php foreach ($list as $l): ?>
		<?php
			// work out the opponent, based on whether it's a home or away fixture
			if (isset($l['TeamB'])) {
				$opponent = [$l['teamb_id'] => $l['TeamB']['name']];
				$ha = 'H';
			} else {
				$opponent = [$l['teama_id'] => $l['TeamA']['name']];
				$ha = 'A';
			}
 		?>
			<tr>
				<td><a title="Click to see match details" href="/matches/<?php echo $l['id']; ?>"><?php echo $l['id']; ?></a></td>
				<td><?php echo date('j M y', strtotime($l['date'])); ?></td>
				<td class="teamflag">
					<div class="flag <?php 
					echo isset($l['Competition']['country']) ? $l['Competition']['country'] : 'en' ; ?>">
					<?php echo (isset($l['Competition']['name'])) ? __('<a href="/competitions/%s">%s</a>', $l['competition_id'], $l['Competition']['name']) : '&nbsp;';	?>
					</div>
				</td>
				<td>
					<a href="/teams/<?php echo key($opponent); ?>"><?=current($opponent); ?></a> (<?=$ha; ?>) 
				</td>
				<td><?php echo ($ha == 'H') ? $l['score'] : strrev($l['score']); ?></td>
				<td>
					<?php 
						if ($l['game'] & 1) echo '<span class="label">G</span> ';
						if ($l['game'] & 2) echo '<span class="label alert">T</span> ';
						if ($l['gotw']) echo '<span title="Game of the Week" class="label info">GotW</span>'; 
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

</section>