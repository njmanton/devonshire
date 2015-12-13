<!-- File: /app/View/Leagues/add.ctp -->
<?php $this->set('title_for_layout', 'Request new league'); ?>

<?php echo $this->Session->flash(); ?>
<h2>Add a new League</h2>
<p>Complete the form below to request a new league created. If the league is approved by an administrator it will be created with you as the organiser.</p>
<p>If you set the league to be public, any player can join the league. The default setting is private, which means any requests to join the league will be sent to you as organiser to approve or reject.</p>
<div class="userform">
<?php
echo $this->Form->create('League');
echo '<fieldset>';
echo $this->Form->input('name');
echo $this->Form->input('description', ['type' => 'textarea', 'maxlength' => 255]);
echo '</fieldset>';
echo $this->Form->input('public', ['type' => 'checkbox']);
echo $this->Form->input('admin', ['type' => 'hidden', 'value' => $user['id']]);
echo $this->Form->input('organiser_name', ['type' => 'hidden', 'value' => $user['username']]);
echo $this->Form->input('pending', ['type' => 'hidden', 'value' => 1]);
?>

	<input type="submit" value="update" class="button tiny" />

</div>
<script>
	$(document).ready(function() {
		$('#LeagueDescription').on('keyup', function() {
			var chr = $('#LeagueDescription').val().length;
			$('#LeagueDescription').prev().html('Description <span class="">[' + (1024 - chr) + ' chars left]</span>');
		});	
	});
</script>