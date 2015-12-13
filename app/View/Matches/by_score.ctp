<!-- app/View/Matches/byScore.ctp -->
<?php
	$this->set('title_for_layout', 'Matches ending ' . $this->passedArgs[0]);
	$gm = '<span class="label">Goalmine</span>';
	$odds = '<span class="label label-success">Tipping</span>';
	$killer = '<span class="label label-important">Killer</span>';
?>
<section>
	<header>
		<h2>Matches ending <?php echo $this->passedArgs[0]; ?></h2>
		<p>Below is a list of all matches in Goalmine that finished <?php echo $this->passedArgs[0]; ?></p>
	</header>

	<table class="table">
		<thead>
			<tr>
				<th>Date</th>
				<th>Competition</th>
				<th>Fixture</th>
				<th>Game</th>
			</tr>
		</thead>

		<tbody>
		<?php if (empty($matches)) echo '<tr><td style="text-align: center;" colspan="4">No matches</td></tr>'; ?>	
		<?php foreach ($matches as $m): ?>
			<tr>
				<td><?php echo date('jS M', strtotime($m['Match']['date'])); ?></td>
				<td><a href="/competitions/view/<?php echo $m['Competition']['id']; ?>"><?php echo $m['Competition']['name']; ?></a></td>
				<td><a href="/matches/view/<?php echo $m['Match']['id']; ?>"><?php echo $m['TeamA']['name'] . ' v ' . $m['TeamB']['name']; ?></a></td>
				<td>
					<?php if ($m['Match']['game'] & 1) echo $gm; ?>
					<?php if ($m['Match']['game'] & 2) echo $odds; ?>
					<?php if ($m['Match']['game'] & 4) echo $killer; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>

	</table>
</section>
