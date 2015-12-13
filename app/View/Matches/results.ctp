<!-- app/View/Matches/results.ctp -->
<?php $this->set('title_for_layout', __('Week %s results', $this->passedArgs[0])); ?>
<section>
	<header>
		<h2>Enter Results for Week <?php echo $this->passedArgs[0]; ?></h2>
	</header>
	<?php
		echo $this->Form->create();
		$check = 0;
		if ($status) {
			echo $this->Form->input('status', [
				'div' => false, 
				'label' => 'This week is set as complete. Check the box if you really want to edit results',
				'type' => 'checkbox'
			]);
			$check = 1;
		}
		$gm = ' <span class="label">G</span>';
		$odds = ' <span class="label alert">T</span>';
		$killer = ' <span class="label success">K</span>';
		$gotw = ' <span title="Game of the Week" class="label">GotW</span>';
	?>
	<table class="table">
		<thead>
			<tr>
				<th>Date</th>
				<th>Competition</th>
				<th>Match</th>
				<th>Result</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($results as $r): ?>
			<?php
				$now = new DateTime();
				$md = new DateTime($r['Match']['date']);
				$disabled = ($now < $md) ? 'disabled' : '';
			?>
				<tr>
					<td><?php echo date('j M y', strtotime($r['Match']['date'])); ?></td>
					<td><?php echo $r['Competition']['name']; ?></td>
					<td>
						<?php
							echo $r['TeamA']['name'] . ' v ' . $r['TeamB']['name'];
							if ($r['Match']['game'] & 1) echo $gm;
							if ($r['Match']['game'] & 2) echo $odds;
							if ($r['Match']['game'] & 4) echo $killer;
							if ($r['Match']['gotw']) echo $gotw;
						?>
					</td>
					<td>
						<input class="input-mini" <?php echo $disabled; ?> type="text" autocomplete="off" name="data[<?php echo $r['Match']['id']; ?>][score]" value="<?php echo $r['Match']['score']; ?>" />
						<input type="hidden" name="data[<?php echo $r['Match']['id']; ?>][dirty]" value=0 />
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class="btn-submit">
		<input type="submit" value="update" class="button small" />
	</div>
</section>
<script>
	
	$(document).ready(function () {
		
		if (<?php echo $check; ?>) {
			$('table :text').attr('disabled', 'disabled');
		}

		$('#MatchStatus').on('click', function() {
				if ($(this).is(':checked')) {
					$(':text').attr('disabled', false);
				} else {
					$(':text').attr('disabled', 'disabled');
				}
		});

		$(':text').on('change', function() {
			$(this).next().val(1);
		});

	});

</script>
