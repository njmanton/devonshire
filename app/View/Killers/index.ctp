<!-- /app/View/Killers/index.php -->
<?php $this->set('title_for_layout', 'Killer index'); ?>
<section>
	<header>
		<h2>Killer games</h2>
		<p>This is a list of all Killer games</p>
	</header>
	<p>&nbsp;</p>
	<table class="table">
		<thead>
			<tr>
				<th>Game</th>
				<th>Description</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($games as $g): ?>
			<tr>
				<td><a href="/killers/<?php echo $g['Killer']['id']; ?>"><?php echo $g['Killer']['id']; ?></a></td>
				<td><?php echo $g['Killer']['description']; ?></td>
				<td><?php echo ($g['Killer']['complete'] == 1) ? 'Completed' : 'On-going'; ?></td>		
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php if ($user): ?>
	<a href="/killers/add" class="button tiny">Create New Game</a>
	<?php endif; ?>
</section>