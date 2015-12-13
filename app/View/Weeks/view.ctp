<!-- /app/View/Weeks/view.ctp -->
<?php $this->set('title_for_layout', 'Goalmine week ' . $week['Week']['id']); ?>
<section>
	<header>
		<h2>Week <?php echo $week['Week']['id']; ?></h2>
		<p></p>
	</header>
	<table class="table">
		<thead>
			<tr>
				<th>Game</th>
				<th>Date</th>
				<th>Competition</th>
				<th>Match</th>
				<th>Result</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($week['Match'])): ?>
			<tr>
				<td>No matches yet</td>
			</tr>
		<?php endif; ?>
		<?php foreach ($week['Match'] as $w): ?>
			<tr>
				<td>
					<?php echo ($w['game'] & 1) ? '<span class="label">G</span>' : ''; ?>
					<?php echo ($w['game'] & 2) ? '<span class="label alert">T</span>' : ''; ?>
				</td>
				<td><?php echo date('jS M' ,strtotime($w['date'])); ?></td>
				<td>
					<a href="/competitions/<?php echo $w['competition_id']; ?>"><?php echo $w['Competition']['name']; ?></a>
				</td>
				<td><a href="/matches/<?php echo $w['id']; ?>">
					<?php echo $w['TeamA']['name'] . ' v ' . $w['TeamB']['name']; ?>
					</a>
				</td>
				<td><?php echo $w['score']; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	
</section>

