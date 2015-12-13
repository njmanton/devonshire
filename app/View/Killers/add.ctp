<?php $this->set('title_for_layout', 'new Killer game'); ?>
<section>
	<header>
		<h2>Set up new game</h2>
		<p></p>
	</header>

	<p>Use this screen to set up a new game of Killer. This will be accessed by <code>/killers/<?=$new_game_number; ?></code>. Select the players taking part from the form below, and press submit. Only players registerd for Killer are shown.</p>

	<form method="post" action="/killers/add">
		
		<fieldset>
			<label for="players">Select players (ctrl/cmd+ for multiple)</label>
			<select name="data[Players][]" id="players" multiple="multiple" style="height: 200px;">
			<?php foreach($players as $k=>$p): ?>
				<option value="<?php echo $k; ?>"><?php echo $p; ?></option>
			<?php endforeach; ?>
			</select>
			<label for="desc">Description</label>
			<textarea name="data[Description]" cols="30" rows="3"></textarea>
			<label for="weeks">Start Week</label>
			<select name="data[Week]" id="weeks">
			<?php foreach($weeks as $k=>$w): ?>
				<option value="<?php echo $k; ?>"><?php echo __('%s (w/c %s)', $k, date('jS M', strtotime($w))); ?></option>
			<?php endforeach; ?>
			</select>
		</fieldset>

		<input type="submit" class="button tiny" />
	</form>

</section>
