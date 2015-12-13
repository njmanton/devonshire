<?php //$this->layout = 'flash'; ?>
<?php $this->set('title_for_layout', 'Login'); ?>
<section class="login">
	<header>
		<h2>LOGIN</h2>
	</header>
	<div class="userform">
		<?php echo $this->Session->flash(); ?>
		<?php echo $this->Form->create('User');?>
			<fieldset>
				<label for="username" class="control-label">Username</label>
				<div class="input-prepend">
					<span class="add-on"><i class="icon-user"></i></span>
					<input class="span4" name="data[User][username]" id="username" type="text" placeholder="username" />
				</div>	
				    
				<label for="pwd" class="control-label">Password</label>
				<div class="input-prepend">
					<span class="add-on"><i class="icon-lock"></i></span>
					<input class="span4" name="data[User][password]" id="pwd" type="password" placeholder="password" />
				</div>
			</fieldset>
			<input type="submit" value="Login" class="button small" />
		<p><a href="/users/forgot">Forgot</a> password?</p>
	</div>
	
</section>



