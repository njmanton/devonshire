<!-- /app/View/Competitions/view.ctp -->
<?php $this->set('title_for_layout', $matches['Competition']['name']); ?>
<section>
	<header>
		<h2><?php echo $matches['Competition']['name']; ?></h2>
		<p></p>
	</header>
	
	<table class="table">
		<thead>
			<tr>
				<th>Week</th>
				<th>Date</th>
				<th>Match</th>
				<th>Result</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($matches)) echo '<tr><td style="text-align: center;" colspan="4">No matches yet</td></tr>'; ?>
		<?php foreach ($matches['Match'] as $m): ?>
			<tr>
				<td><a href="/weeks/<?php echo $m['week_id']; ?>"><?php echo $m['week_id']; ?></a></td>
				<td><?php echo date('j M y', strtotime($m['date'])); ?></td>
				<td>
					<a href="/matches/<?php echo $m['id']; ?>"><?php echo $m['TeamA']['name'] . ' v ' . $m['TeamB']['name']; ?></a>
				</td>
				<td><?php echo $m['score']; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	
</section>
