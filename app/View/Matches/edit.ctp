<?php 
	$w = $week['Week']['id'];
	$this->set('title_for_layout', 'Manage week ' . $w);
?>
<section>
	<header>
		<h2>Edit matches</h2>
		<p>Manage the matches for week <?=$w; ?> here. All changes are automatically saved to the database.</p>
		<p>If a match already has predictions or bets made on it, it cannot be deleted, only edited.</p>
	</header>
	<table class="table">
		<thead>
			<tr>
				<th>Date</th>
				<th>Game(s)</th>
				<th>Home</th>
				<th>Away</th>
				<th>Competition</th>
				<th>1</th>
				<th>X</th>
				<th>2</th>
				<th>GotW</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody id="match-rows">
		<?php foreach ($matches as $m): ?>
			<tr>
				<td><?php echo date('jS M', strtotime($m['Match']['date'])); ?></td>
				<td>
					<?php if ($m['Match']['game'] & 1) echo '<span class="label radius">G</span>'; ?>
					<?php if ($m['Match']['game'] & 2) echo '<span class="label radius alert">T</span>'; ?>
					<?php if ($m['Match']['game'] & 4) echo '<span class="label radius secondary">K</span>'; ?>
				</td>
				<td><?php echo $m['TeamA']['name']; ?></td>
				<td><?php echo $m['TeamB']['name']; ?></td>
				<td><?php echo $m['Competition']['name']; ?></td>
				<td><?php echo $m['Match']['odds1']; ?></td>
				<td><?php echo $m['Match']['oddsX']; ?></td>
				<td><?php echo $m['Match']['odds2']; ?></td>
				<td class="gotw-row"><?php echo ($m['Match']['gotw']) ? '&#10004;' : '&nbsp;' ; ?></td>
				<td>
					<?php 
						$c = count($m['Prediction']) + count($m['Bet']);
						if (!$c) {
							echo $this->Html->link('Delete', ['action' => 'delete', $m['Match']['id']], ['class' => 'del tiny button alert']);
						}''
					?>
					<a href="#" role="button" class="edt button tiny" data-match="<?php echo $m['Match']['id']; ?>">Edit</a>
				</td>
			</tr>
	<?php endforeach; ?>
		</tbody>
	</table>
	<a href="#" role="button" class="button small" data-reveal-id="new-match-box">New Match</a>

	<?=$this->element('matchedit', ['week' => $w]); ?>

</section>

<script>
	$(document).ready(function() {
		var min_date = new Date("<?php echo $week['Week']['start']; ?>");
		initNewMatchBox(min_date);

		$(document).on('closed.fndtn.reveal', '[data-reveal]', function() {
			document.getElementById('new-match-form').reset();
		})

		$('#compdd').on('change', function() {
			$(this).removeClass('inputflag');
		})
	})

</script>


