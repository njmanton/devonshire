<!-- app/View/Predictions/view.ctp -->
<?php 
	$this->set('title_for_layout', 'Predictions Week ' . $week['Week']['id']);
	$total_goals = 0;
	$table_footer = [];
	// pull in data from leagues controller
	$league = $this->requestAction('/standings/view/' . $week['Week']['id']);
	$userid = $user['id'];
	$admin = $user['admin'];
	$now = new DateTime();
	$disable_radios = [];

?>
<section>
	<header>
		<h2>Predictions &ndash; week <?php echo $week['Week']['id']; ?></h2>
		<?php if (isset($complete) && $complete == 1) {
						// show a finalise button, to run end-of-week workflows
						echo $this->element('finalise', ['week' => $week['Week']['id']]);
					}
		?>
		<ul class="tabs" data-tab>
			<li class="tab-title active">
				<a href="#tab1">Grid</a>
			</li>
			<li class="tab-title">
				<a href="#tab2">Standings</a>
			</li>
		</ul>
	</header>
	<div class="tabs-content">
		<div class="content active" id="tab1">
			<!--tab 1 prediction table-->
			<?php echo $this->Form->create(); ?>
			<table class="f32 table grid">
				<thead>
					<tr>
						<th class="grid-header">Date</th>
						<th class="grid-header">Competition</th>
						<th class="grid-header">Match</th>
						<th>Score</th>
						<?php foreach ($players as $k=>$p): ?>
						<th><a href="/users/<?php echo $k; ?>"><?php echo $p['username']; ?></a></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($table as $t): // loop through match details ?>
				<?php 
					$total_goals += array_sum(explode('-', $t['_match']['score']));
				?>
					<tr>
						<td class="grid-header"><?php echo date('D jS M', strtotime($t['_match']['date'])); ?></td>
						<td class="grid-header flag <?=$t['_match']['country']; ?>"><span><?php echo $t['_match']['comp']; ?></span></td>
						<td class="grid-header">
							<a title="<?php echo $t['_match']['fixture'];  if ($t['_match']['gotw']) echo ' (GotW)'; ?>" href="/matches/<?php echo $t['_match']['id']; ?>"><?php echo $t['_match']['fixture']; ?></a><?php if ($t['_match']['gotw']) echo ' (GotW)'; ?>
						</td>
						<td>
							<?php
								// for admin users, show a form control to enter the match result
								$md = new DateTime($t['_match']['date']);
								$md->add(new DateInterval(DEADLINE_OFFSET));
								if (($now >= $md) && $admin && !$week['Week']['status']) {
									echo '<input type="text" class="input-mini" name="data[' . $t['_match']['id'] . '][score]" value="' . $t['_match']['score'] .  '" />';
									echo '<input type="hidden" name="data[' . $t['_match']['id'] . '][scoredirty]" value="0" >';
									if ($t['_match']['gotw'] == true) {
										echo '<input type="hidden" name="data[' . $t['_match']['id'] . '][gotw]" value="1" />';
									}
								} else {
									echo $t['_match']['score'];
								}
							?>
						</td>
						<?php
							// loop through each prediction. if combination of player and match exists, output it
							foreach ($players as $k=>$p) {
								if (array_key_exists($k, $t) || ($k == $userid)) {
									// if there's data for this combination, work out whether the match has
									// expired (past deadline)
									// if match has expired and it was the joker, disable changing joker for all
									// subsequent matches

									// set the expired flag if the match is past expiry, or a score has been entered
									// NEW for 13-14. Deadlines are midday on Saturday for ALL matches
									$wd = new DateTime($week['Week']['start']);
									$wd->add(new DateInterval(DEADLINE_OFFSET));
									$expired = (($now > $wd) || $t['_match']['score']);

									$pred = (isset($t[$k]['pred'])) ? $t[$k]['pred'] : '' ;
									$predid = (isset($t[$k]['predid'])) ? $t[$k]['predid'] : '' ;

									// if the current match has expired and it was the joker, disable all subsequent jokers
									if ($expired && array_key_exists($k, $t) && ($t[$k]['joker']) == 1) {
										$disable_radios[$k] = true;
									}
									
									$radio_status='';

									// if the match has a joker set the radio status
									if (isset($t[$k]['joker']) && $t[$k]['joker'] == 1) {
										$radio_status = 'checked="checked"';
									} 

									// if the match has expired, or jokers already disabled, set the radio status
									if ($expired || (isset($disable_radios[$k]) && $disable_radios[$k])) {
										$radio_status .= ' disabled="disabled" ';
									} 

									if (($k == $userid) && !$expired) {
									// if the prediction belongs to logged in user, show a form control

						 				// if this match has just been updated through a POST, highlight it
										$upd = (isset($updates) && in_array($t['_match']['id'], $updates)) ? 'upd' : '' ;

										echo '<td>
														<input type="text" class="' . $upd . '" name="data[' . $t['_match']['id']. '][pred]" value="' . $pred . '" />
														<input type="hidden" name="data[' . $t['_match']['id'] . '][dirty]" value="1" />
														<input type="hidden" name="data[' . $t['_match']['id'] . '][predid]" value="' . $predid . '" />';
										
										if (!$t['_match']['gotw']) {
											echo '<input type="radio" name="data[joker]" value="' . $t['_match']['id'] . '" ' . $radio_status . ' /></td>';
										}
									} else {
										@$class = 'pts' . $t[$k]['pts'];
										$title = '';
										if (isset($t[$k]['joker']) && $t[$k]['joker'] == 1) {
											$class .= ' joker';
											$title = 'joker match';
										}

										if (HIDE_PREDS === 1) {
											if ($expired) {
												echo '<td class="' . $class . '" title="' . $title . '">' . $pred . '</td>';
											} else {
												echo '<td>?-?</td>';
											}
										} else {
											echo '<td class="' . $class . '" title="' . $title . '">' . $pred . '</td>';
										}
										
									}
									// cumulative sum of goals and points scored
									@$table_footer[$k]['goals'] += array_sum(explode('-',$t[$k]['pred']));
									@$table_footer[$k]['pts'] += $t[$k]['pts'];
								} else {
								// no prediction for that player/match, so output blank cell
									echo '<td>&nbsp</td>';
								}
							}
						?>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
				<?php 
				// now work out the closest in terms of goals for the bonus
				foreach ($table_footer as $k=>$v) {
					$diff[$k] = abs($total_goals - $v['goals']);
				}
				$closest = $this->Score->getmin($diff);

				// construct the footer rows (totals)
				$footer_goals = '<tr><td>&nbsp;</td><td>&nbsp;</td><td>Total Goals</td><td>' . $total_goals . '</td>';
				$footer_pts = '<tr><td>&nbsp;</td><td>&nbsp;</td><td>Total Points</td><td>&nbsp;</td>';

				// loop through the footer array, appending values and classes to cells
				foreach ($table_footer as $k=>&$v) {
					$class = '';
					if (in_array($k, $closest)) {
						$class = ' class = "closest"';
						$v['pts'] += CLOSEST_PTS;
					}
					$footer_goals .= __('<td%s>%s</td>', $class, $v['goals']);
					$footer_pts .= __('<td>%s</td>', $v['pts']);
				}
				// output the rows
				echo $footer_goals . '</tr>';
				echo $footer_pts . '</tr>';
				?>
				</tfoot>
			</table>
			<?php //echo $this->Form->end('Update'); ?>
			<input type="submit" id="pred_submit" value="update" class="button small" />
			
		</div>
		<div class="content" id="tab2">
			<!--tab 2 league table-->
			<table class="table">
				<thead>
					<tr>
						<th>Position</th>
						<th>Player</th>
						<th>Points</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$row=0; // which row are we on
						$rank = 0; // what is the rank fof the player
						$absrank = 0; // what is abs score
						$prevrank = 0; // rank of row n-1

						foreach ($league as $k=>$l) {
							if ($l['points'] == $prevrank) {
								$row++;
								$equal = '=';
							} else {
								$rank = ++$row;
								$equal = '';
							}
					?>
						<tr>
							<td><?php echo $rank . $equal; ?></td>
							<td><a href="/users/<?php echo $k; ?>"><?php echo $l['name']; ?></a></td>
							<td><?php echo $l['points']; ?></td>
						</tr>
					<?php $prevrank = $l['points']; ?>
					<?php } ?>					
				</tbody>
			</table>
		</div>
	</div>

</section>
