<?php echo $this->Html->script('/ckeditor/ckeditor'); ?>
<?php $this->set('title_for_layout', 'Edit Post'); ?>
<section>
	<header>
		<h2>Edit news item</h2>
		<p></p>
	</header>
	<?php 
		echo $this->Form->create('Post', ['action' => 'edit']);
		echo $this->Form->input('title');
		echo $this->Form->input('body', ['rows' => '4', 'class' => 'ckeditor']);
		echo $this->Form->input('id', ['type' => 'hidden']);
		echo $this->Form->input('sticky');
		echo '<input type="submit" value="Edit" class="button tiny" />';
		echo $this->Form->end();
 ?>
		
</section>