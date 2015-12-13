<!-- File: /app/View/Leagues/manage.ctp -->
<?php echo $this->Session->flash(); ?>

<?php if (($user['id'] == $league['League']['admin']) || ($user['admin'] == 1)): ?>

	<h2>Manage <?php echo $league['League']['name'] ; ?></h2>

	<div class="userform">
	<?php
		echo $this->Form->create('League'); 
		echo '<fieldset>';
		echo $this->Form->input('name', ['value' => $league['League']['name']]);
		echo $this->Form->input('description', ['rows' => 2, 'value' => $league['League']['description']]);
		echo $this->Form->input('id', ['type' => 'hidden', 'value' => $league['League']['id']]);
		echo '</fieldset>';
		$pending_users = false;
		foreach ($league['LeagueUser'] as $l) {
			if ($l['confirmed'] == 0) { $pending_users = true; }
		}
	?>
	
	<?php	if ($pending_users === true): ?>
		<hr />
		<h3>Applications to join league</h3>
		<table class="table">
			<thead>
				<th>Name</th>
				<th>Accept</th>
				<th>Reject</th>
			</thead>
			<tbody>
			<?php foreach($league['LeagueUser'] as $a): ?>
				<?php if ($a['confirmed'] == 0): ?>	
					<tr>
						<td><?php echo $a['User']['username']; ?></td>
						<td><input type="radio" value="a" name="Manage[<?php echo $a['id']; ?>]"  /></td>
						<td><input type="radio" value="r" name="Manage[<?php echo $a['id']; ?>]"  /></td>
						<td><input type="hidden" value="<?php echo $a['User']['id']; ?>" name="Request[id]" /></td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
			</tbody>
		</table>
		<div class="btn-submit">
			<input type="submit" value="update" class="button tiny" />
		</div>
	<?php endif; ?>
	
<?php else: ?>
	echo '<p class="alert">Sorry, you must be an administrator or the league organiser to manage this league</p>';
<?php endif; ?>
	
<script>
	$(document).ready(function() {
		$('#LeagueDescription').on('keyup', function() {
			var chr = $(this).val().length;
			$(this).prev().html('Description <span>[' + (1024-chr) + ' chars left]</span>');
		}).on('blur', function() {
			$(this).prev().html('Description');
		});
	});
</script>


