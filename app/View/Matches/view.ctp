<!-- app/View/Matches/view.ctp -->
<?php
	$this->set('title_for_layout', __('%s | %s v %s', APP_NAME, $m['TeamA']['name'], $m['TeamB']['name']));
	// get the match components
	@list($h,$a) = explode('-', $m['Match']['score']);
	$total = 0;

	$dt = new DateTime($m['Match']['date']);
	$dt->add(new DateInterval(DEADLINE_OFFSET));
	$now = new DateTime();

?>

<section class="small-10 small-centered columns">
	
	<table class="matchresult">
		<caption><?=__('<a href="/competitions/%s">%s</a>, %s', $m['Match']['competition_id'], $m['Competition']['name'], date('j M Y', strtotime($m['Match']['date']))); ?></caption>
		<tbody>
			<tr>
				<td class="teams">
					<a href="/teams/<?=$m['Match']['teama_id']; ?>"><?=$m['TeamA']['name']; ?></a>
				</td>
				<td class="score"><?=$m['TeamA']['goals']; ?></td>
			</tr>
			<tr>
				<td class="teams">
					<a href="/teams/<?=$m['Match']['teamb_id']; ?>"><?=$m['TeamB']['name']; ?></a>
				</td>
				<td class="score"><?=$m['TeamB']['goals']; ?></td>
			</tr>
		</tbody>
	</table>
	
	<header>
		<ul class="tabs" data-tab>

		<?php if (($m['Match']['game'] & 1) != 0): ?>
			<li class="tab-title active"><a href="#tab1">Goalmine</a></li>
		<?php endif; ?>

		<?php if (($m['Match']['game'] & 2) != 0): ?>
			<li class="tab-title"><a href="#tab2">Tipping</a></li>
		<?php endif; ?>

		</ul>
	</header>
	<div class="tabs-content">

		<?php if (HIDE_BETS === 0 || ($now > $dt)): ?>
		<div class="content <?php echo (count($m['Prediction'])) ? 'active' : ''; ?>" id="tab1">
			<table class="table">
				<thead>
					<tr>
						<th>Player</th>
						<th>Prediction</th>
						<th style="width: 30px; !important">Pts</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($m['Prediction'] as $p): ?>
					<tr>
						<td><a href="/users/<?php echo $p['user_id']; ?>"><?php echo $p['User']['username']; ?></a></td>
						<td><?php echo $p['pred']; ?></td>
							<?php 
								$joker = ($p['joker'] == 1) ? ' joker" title="joker match' : '';
								$class = 'pts' . $p['points'];
							?>
 						<td class="<?php echo $class . $joker; ?>"><?php echo $p['points']; ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="content <?php echo (count($bets) && !count($m['Prediction'])) ? 'active' : ''; ?>" id="tab2">

			<?php if (!is_null($m['Match']['odds1'])): ?>
				<span class="label radius" id="o1"><?php echo $m['Match']['odds1']; ?></span>  <?php echo $m['TeamA']['name']; ?>
				<span class="label radius" id="ox"><?php echo $m['Match']['oddsX']; ?></span>  <?php echo $m['TeamB']['name']; ?>
				<span class="label radius" id="o2"><?php echo $m['Match']['odds2']; ?></span>
			<?php endif; ?>

			<table class="table">
				<thead>
					<th>Player</th>
					<th>Bet</th>
					<th>Amount</th>
					<th>Outcome</th>
				</thead>
				<tbody>
				<?php if (empty($bets)): ?>
					<tr>
						<td colspan=4>No bets yet</td>
					</tr>
				<?php else: ?>
				<?php foreach ($bets as $b): ?>
					<tr<?php if ($b['outcome'] > 0) {echo ' class="hilite" '; } ?>>
						<td><a href="/users/<?php echo $b['user_id']; ?>"><?php echo $b['username']; ?> </a></td>
						<td><?php echo $b['prediction']; ?></td>
						<td><?php echo '$' . $b['amount']; ?></td>
						<td><?php echo aus($b['outcome']); $total += $b['outcome']; ?></td> 
					</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>Total</td>
						<td><?php echo aus($total); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php else: ?>
			<p>Predictions hidden until deadline</p>
		<?php endif; ?>

	</div>

</section>

<script>
	/*$(document).ready(function() {
			$.ajax({
				url: '/bets/bets_by_match/' + <?=$m['Match']['id']; ?>,
				success: function (response) {
					console.log (response);
				}

			});

	})*/
</script>
