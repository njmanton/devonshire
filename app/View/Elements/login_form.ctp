<!-- app/View/elements/login_form.ctp -->

<?php echo $this->Form->create(null, ['url' => '/users/login']); ?>
	<section id="login_modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	    <h3 id="myModalLabel">Login</h3>
	  </div>
	  <div class="modal-body"> 

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

			<p><a href="/users/forgot">Forgot</a> password?</p>
	 
	  </div>
	  <div class="modal-footer">
	    <input type="submit" class="btn" value="Login" />
	  </div>
	</section>
<?php echo $this->Form->end(); ?>