<!-- app/View/Teams/add.ctp -->
<?php $this->set('title_for_layout', 'Add Team'); ?>

<section class="small-12 columns">
	<header>
		<h2>Add a team</h2>
		<p>Add a new team to the database. You must do this before you can include a new match with that team.</p>
	</header>
	
	<?php echo $this->Form->create(); ?>
	
	<fieldset class="control-group">
		<label class="control-label" for="new-team">New Team</label>
		<input type="text" name="Team[name]" id="new-team" />
		<span class="help-inline" id="err"></span>
		<label>New short name (optional)</label>
		<input type="text" name="Team[sname]" />
	</fieldset>
	<input type="submit" value="Add" class="button tiny">
	
</section>

<script>
	$(document).ready(function() {

		$('#new-team').autocomplete({
			source: "/teams/show",
			minLength: 3,
			delay: 0,
			select: function(event, ui) {
				if (ui.item) {
					$(this).parent().addClass('error').find('#err').text('That name already exists in the database');
					return false;
				} else {
					$(this).parent().removeClass('error').find('#err').text('');
				}
			}
		});

	});
</script>
