<?php $this->set('title_for_layout', 'Teams'); ?>
<section class="small-12 columns">
	<header>
		<h2>All teams</h2>
		<p>List of all teams in Goalmine, together with their match statistics. Click on a column header to sort by that column.</p>
	</header>

	<table id="all-teams">
		<thead>
			<tr>
				<th>Team</th>
				<th>Appearances</th>
				<th><abbr title="Games of the Week">(GotW)</abbr></th>
				<th>Wins</th>
				<th>Draws</th>
				<th>Defeats</th>
				<th><abbr title="Points per game">PPG</abbr></th>
			</tr>
		</thead>

		<tbody>
		<?php foreach ($teams as $k=>$t): ?>
		<?php $apps = ($t['w'] + $t['d'] + $t['l']); ?>
			<tr>
				<td><a href="/teams/<?php echo $k; ?>"><?php echo $t['name']; ?></a></td>
				<td><?=$apps; ?></td>
				<td><?=$t['gotw']; ?></td>
				<td><?=$t['w']; ?></td>
				<td><?=$t['d']; ?></td>
				<td><?=$t['l']; ?></td>
				<td><?php if ($apps > 0) { echo number_format(($t['w'] * 3 + $t['d']) / $apps, 2); } ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>

	</table>

</section>
<script src="/js/vendor/jquery.dataTables.min.js"></script>
<script>
	$(document).ready(function() {
		$('#all-teams').dataTable({
			'paging': false,
			'searching': false,
			'order': []
		});
	});
</script>