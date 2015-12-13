<!-- /app/View/Users/update.ctp -->
<?php $this->set('title_for_layout', 'Update User'); ?>
<?php
	$chk = 'checked';
	// create an array of the notification preferences
	$prefs = $data['User']['preferences'];
?>

<section class="row">
	<header>
		<?php echo $this->Session->flash(); ?>
		<h2>Update details for <?php echo $data['User']['username']; ?></h2>
	</header>
	
	<?php echo $this->Form->create('UserDetails', ['class' => 'form-horizontal']); ?>
	<div>
		<input name="data[UserDetails][id]" type="hidden" value="<?php echo $data['User']['id']; ?>" id="UserDetailsId" />
		<fieldset>
			<div class="control-group">
				<label for="UserDetailsEmail" class="control-label">Email</label>
				<div class="controls">
					<input name="data[UserDetails][email]" type="text" id="UserDetailsEmail" value="<?php echo $data['User']['email']; ?>" />
				</div>
			</div>
			
			<div class="control-group">
				<label for="UserDetailsCurrent" class="control-label">Current Password</label>
				<div class="controls">
					<input name="data[UserDetails][current]" type="password" id="UserDetailsCurrent" placeholder="current password" /><span class="help-inline" id="current_err"></span>	
				</div>
			</div>
			
			<div class="control-group">
				<label for="UserDetailsNew" class="control-label">New Password</label>
				<div class="controls">
					<input name="data[UserDetails][new]" type="password" id="UserDetailsNew" placeholder="new password" /><span class="help-inline" id="new_err"></span>	
				</div>
			</div>
			
			<div class="control-group">
				<label for="UserDetailsRepeat" class="control-label">Repeat Password</label>
				<div class="controls">
					<input name="data[UserDetails][repeat]" type="password" id="UserDetailsRepeat" placeholder="repeat new password"/><span class="help-inline" id="repeat_err"></span>	
				</div>
			</div>
			
		</fieldset>
		<input type="submit" value="Update" class="button small" />
	</div>

</section>


