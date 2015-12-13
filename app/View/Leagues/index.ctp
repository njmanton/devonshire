<!-- File: /app/View/Leagues/index.ctp -->
<?php 
$this->set('title_for_layout', 'User-defined leagues');
echo $this->Session->flash(); 
?>

<h2>User Leagues</h2>
<p>Below is a list of user-created leagues for GoalMine. Click on a league to see the current standings for that league. Leagues marked with an asterisk(*) are private leagues, you can see the standings for these leagues, but to join you must be approved by that league's organiser. Other leagues are public, and you may join those without restriction.</p>
<table class="table">
	<thead>
		<th>League</th>
		<th>Members</th>
	</thead>
	<tbody>
	<?php if (empty($leagues)) echo '<tr><td style="text-align: center;" colspan="2">No user leagues yet</td></tr>'; ?>
	<?php foreach ($leagues as $l): ?>
		<tr>
			<td><a href="/leagues/view/<?php echo $l['League']['id']; ?>"><?php echo $l['League']['name']; ?></a>
			<?php if ($l['League']['type'] == 0) echo '*'; ?></td>
			<td><?php echo $l[0]['cnt']; ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if (!empty($pending) && $user['admin'] == 1): ?>
	<h3>Pending user league applications</h3>
	<?php echo $this->Form->create('Manage'); ?>
	<table class="table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Creator</th>
				<th>Accept</th>
				<th>Reject</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($pending as $p): ?>
			<tr>
				<td><?php echo $p['League']['name']; ?></td>
				<td title="<?php echo $p['League']['description']; ?>">
					<div class="truncate"><?php echo $p['League']['description']; ?></div>
				</td>
				<td><?php echo $p['User']['username']; ?></td>
				<td>
					<input type="radio" name="Manage[<?php echo $p['League']['id']; ?>]" value="a" />
				</td>
				<td>
					<input type="radio" name="Manage[<?php echo $p['League']['id']; ?>]" value="r" />
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
	</table>
	<input type="submit" class="button tiny" value="Update" />
	
<?php endif; ?>
<?php if ($user) echo $this->Html->link('Request a new league', ['controller'=>'leagues', 'action'=>'add']);
?>


