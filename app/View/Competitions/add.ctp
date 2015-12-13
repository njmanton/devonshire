<!-- app/View/Competitions/add.ctp -->
<?php $this->set('title_for_layout', 'Add a competition') ?>

<section class="small-12 columns">
	<header>
		<h2>Add a competition</h2>
		<p>Add a new competition to the database. You must do this before you can include a new match with that competition.</p>
	</header>

	<?php echo $this->Form->create('Competition', ['action' => 'add']); ?>
	<fieldset class="control-group">
		<label class="control-label" for="new-comp">New Competition</label>
		<input type="text" name="Competition[name]" id="new-comp" />
		<span class="help-inline" id="err"></span>
	</fieldset>
		<input type="submit" value="Add" class="button tiny" />
</section>

