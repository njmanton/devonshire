<!-- File: /app/View/Leagues/view.ctp -->
<?php
	$rank = 1;
	$this->set('title_for_layout', 'View League');
?>

<section>
<?php	if (empty($league)): ?>
	<p class="alert alert-box warning">Sorry, that league does not exist!</p>
<?php else: ?>

	<h2><?php echo $league['League']['name']; ?></h2>
	<h4><p>Organiser: <a href="/users/view/<?php echo $league['User']['id']; ?>"><?php echo $league['User']['username']; ?></a>
	<p><?php echo (empty($league['League']['description'])) ? '<p><em>no description</em></p>' : $league['League']['description']; ?></p>
	<h5><?php echo ($league['League']['type'] == 0) ? 'private' : 'public'; ?></h5>

	<?php
		$cnt = '';
		// if user is organiser of the league, show a manage link
		if (($league['League']['admin'] == $user['id']) || ($user['admin'] == 1)) {
			echo $this->Html->link('Manage this league', ['controller'=>'leagues','action'=>'manage',$league['League']['id']]);
			$cnt = $pending[0][0]['cnt'];
			$plural = ($cnt > 1)?' requests':' request';
			if ($cnt > 0) echo ' (' . $cnt . $plural . ' pending)';
		}

		// loop through LeagueUsers to se if logged-in user already member of this league
		$player_in_league = 0;
		$player_pending = 0;
		foreach ($league['LeagueUser'] as $lu) {
			if ($lu['user_id'] == $user['id']) {
				if ($lu['confirmed'] == 1) {
					$player_in_league = 1;
				} else {
					$player_pending = 1;
				}
			}
		}
	?>

	<table class="table league">
		<thead>
			<tr>
				<th>Position</th>
				<th>Player</th>
				<th>Points</th>
			</tr>
		</thead>
		<tbody>
		<?php if (empty($standings)) echo '<tr><td style="text-align: center;" colspan="3">No current players</td</tr>'; ?>
		<?php foreach($standings as $s): ?>
				<tr <?php if ($user['id']==$s['U']['id']) echo 'class="hiliterow"'; ?>>
					<td><?php echo $rank++; ?></td>
					<td><a href="/users/view/<?php echo $s['U']['id']; ?>"><?php echo $s['U']['username']; ?></a></td>
					<td><?php echo $s['0']['pts']; ?></td>
				</tr>
		<?php if ($s['U']['id'] == $user['username']) $player_in_league = 1; ?>
		<?php endforeach; ?>
		</tbody>
	</table>

	<?php
		if ($user) {
			if ($player_pending == 1) { // user has pending application
				echo 'You currently have a pending application to join this league.';
			} elseif ($player_in_league == 1) { // user is already in league
				echo '<p id="leave"><a href="/leagues/leave/' . $league['League']['id'] . '">Remove yourself from this league</a></p>';
			}	else { // user not in league at all
				$label = (($league['League']['type'] == 1)?'Join ':'Apply to join ') . ' this league';
				echo __('<p id="join"><a href="/leagues/apply/%s">%s</a>', $league['League']['id'], $label);
			}
		} 
	?>
<?php endif; ?>
</section>

<script>
	$(document).ready(function() {
		$('#leave').click(function() {
			return(confirm('Are you sure you wish to leave this league?'));
		});
		$('#joim').click(function() {
			return(confirm('Are you sure you wish to join this league?'));
		});

	});
</script>