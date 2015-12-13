
<?php $data = $this->requestAction('/leagues/thirtyclub'); ?>

<section>
	<header>
		<h2>The 30 club</h2>
		<p>The table below is a list of all the players who have achieved a score of 30 points or more in Goalmine</p>
	</header>

	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Score</th>
				<th>In Week</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data as $d): ?>
			<tr>
				<td><a href="/users/view/<?php echo $d['User']['id']; ?>"><?php echo $d['User']['username']; ?></a></td>
				<td><?php echo $d['League']['points']; ?></td>
				<td><?php echo $d['League']['week_id']; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</section>