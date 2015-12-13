<?php echo $this->Html->script('/ckeditor/ckeditor'); ?>
<?php $this->set('title_for_layout', 'Add Post'); ?>

<section>

		<h2>Add a new news item</h2>
		<p>Use the form below to add a new news item. Items are shown in descending date order. Sticky items always appear first.</p>

	<form action="/posts/add" method="post" id="PostAddForm">
		<input type="hidden" value="<?php echo $user['id']; ?>" name="data[Post][user_id]" />
		<label for="PostTitle">Title</label>
		<input name="data[Post][title]" type="text" id="PostTitle" />
		<label for="PostBody"></label>
		<textarea name="data[Post][body]" class="ckeditor" id="PostBody" cols="30" rows="10">
			
		</textarea>
		<label for="PostSticky">Sticky
			<input type="checkbox" data="data[Post][sticky]" id="PostSticky" value="0" />
		</label>
		
		<input type="submit" class="button small" />
	</form>
</section>