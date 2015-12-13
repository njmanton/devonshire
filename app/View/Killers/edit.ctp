<?php $this->set('title_for_layout', 'edit Killer game'); ?>
<section>
	<header>
		<h2>Edit killer game</h2>
		<p>Use this screen to edit a game of Killer. Select the additional players taking part from the form below, and press submit.</p>
	</header>

	<form method="post" action="/killers/edit">
		
		<fieldset>
			<label for="players">Select players (ctrl/cmd+ for multiple)</label>
			<select name="data[Players][]" id="players" multiple="multiple" style="height: 200px;">
			<?php foreach($players as $k=>$p): ?>
				<option value="<?php echo $k; ?>"><?php echo $p; ?></option>
			<?php endforeach; ?>
			</select>
			<label for="desc">Description</label>
			<textarea name="data[Description]" cols="30" rows="3"></textarea>
		</fieldset>

		<input type="submit" class="tiny button" />
	</form>

</section>
