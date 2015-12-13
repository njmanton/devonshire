<!-- /app/View/Bets/edit.ctp -->
<?php 
	$this->set('title_for_layout', 'Make bets');
?>
<style>
	.btn { width: 4em;}
	.disablerow {font-style: italic; color: #999;}
</style>
<section>
	<header>
		<h3>Bets for week <?php echo $this->passedArgs[0]; ?></h3>
		<p>Deadline for this week is: <?php echo $deadline->format('H:i jS M') . ' (' . $deadline->setTimezone(new DateTimeZone('Australia/Sydney'))->format('H:i') . ' Sydney)'; ?></p>
	</header>

	<p>Your total amount bet: £<span id="totamount">-</span> on <span id="totmatches"></span>. Your maximum return is: £<span id="totreturn"></span></p>

	<?php echo $this->Form->create(); ?>
	<table class="betedit">
		<thead>
			<tr>
				<th>Select</th>
				<th>Match</th>
				<th>Date</th>
				<th>Odds [1|X|2]</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($bets as $b): ?>
				<tr>
					<td>
						<input type="hidden" name="data[<?php echo $b['Match']['id']; ?>][dirty]" value="0" />
						<input type="hidden" name="data[<?php echo $b['Match']['id']; ?>][betid]" value="<?php if (isset($b['Bet']['id'])) echo $b['Bet']['id']; ?>" />
						<input type="checkbox" name="data[<?php echo $b['Match']['id']; ?>][sel]" <?php if (isset($b['Bet']['amount']) && $b['Bet']['amount']>0) echo 'checked="checked"'; ?>	/>
					</td>
					<td>
					<?php 
					echo $b['TeamA']['name'] . ' v ' . $b['TeamB']['name'];
					if (isset($updates) && in_array($b['Match']['id'], $updates)) {
						echo ' &#x2713;';
					}
					?>
					</td>
					<td><?php echo date('jS M y', strtotime($b['Match']['date'])); ?></td>
					<td>
						<div class="odds-buttons">
							<button data-sel="1" type="button" class="button tiny <?php if (isset($b['Bet']['prediction']) && $b['Bet']['prediction'] == '1') echo 'alert'; ?> "><?php echo $b['Match']['odds1']; ?></button>
							<button data-sel="X" type="button" class="button tiny <?php if (isset($b['Bet']['prediction']) && $b['Bet']['prediction'] == 'X') echo 'alert'; ?> "><?php echo $b['Match']['oddsX']; ?></button>
							<button data-sel="2" type="button" class="button tiny <?php if (isset($b['Bet']['prediction']) && $b['Bet']['prediction'] == '2') echo 'alert'; ?> "><?php echo $b['Match']['odds2']; ?></button>
						</div>
						<input type="hidden" name="data[<?php echo $b['Match']['id']; ?>][pred]" value="<?php if (isset($b['Bet']['prediction'])) echo $b['Bet']['prediction']; ?>" />
					</td>
					<td>
						<input class="bet-amt amt" max="60" min="20" name="data[<?php echo $b['Match']['id']; ?>][amt]" value="<?php if (isset($b['Bet']['amount'])) echo $b['Bet']['amount']; ?>" />
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class="btn-submit">
		<input type="submit" id="submitform" value="Update" class="button tiny" />
	</div>
</section>

<script>

	$(document).ready(function() {

		$('.odds-buttons button').on('click', function() {
			var parent = $(this).parent();
			parent && parent.find('.alert').removeClass('alert');
			$(this).toggleClass('alert');
		})

	})

</script>