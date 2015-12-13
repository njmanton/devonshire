<?php $this->set('title_for_layout', 'Posts'); ?>
<section>
	<header>
		<h2>News</h2>
		<p>
			<?php if ($user['admin']): ?>
				<a href="/posts/add" class="button tiny">Add new post</a>
			<?php endif; ?>
		</p>
	</header>
	
	<?php if (empty($posts)) echo '<p class="alert alert-notice">No posts in last 30 days. <a href="/posts/index/9999">Show all?</a></p>'; ?>
	<?php foreach($posts as $p): ?>
	
	<article class="newsitems">
		<header>
			<h4><?php echo $p['Post']['title']; ?></h4>
		</header>
		<div class="newsbody">
			<?php echo $p['Post']['body']; ?>
		</div>
		<footer>
			<div>
				<p>
					Posted by: <a href="/users/view/<?php echo $p['Post']['user_id']; ?>"><?php echo $p['User']['username']; ?></a> on: 
					<?php
						echo date('jS M y', strtotime($p['Post']['created'])); 
						if (isset($p['Post']['modified']) && ($p['Post']['modified'] > $p['Post']['created'])) {
							echo ', Modified: ' . date('jS M y', strtotime($p['Post']['modified']));
						}
						if ($user['id'] == $p['Post']['user_id']) echo $this->Form->postLink('Delete', ['action' => 'delete', $p['Post']['id']], ['confirm' => 'Are you sure?']) . ' | ' . '<a href="/posts/edit/' . $p['Post']['id'] . '">Edit</a>'; 
					?>
				</p>
			</div>
		</footer>
	</article>
	
	<?php endforeach; ?>
	
</section>
