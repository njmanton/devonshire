<?php $posts = $this->requestAction('/posts/index/5'); ?>
<?php foreach ($posts as $post): ?>
	<article class="news-item">
		<header>
			<h4><?=$post['Post']['title']; ?></h4>
		</header>
		<div class="news-body">
			<?=$post['Post']['body']; ?>
		</div>
		<footer>
				Posted by <a href="/users/<?=$post['Post']['user_id']; ?>"><?=$post['User']['username']; ?></a> on <?=date('j M y h:ia', strtotime($post['Post']['created'])); ?> 
				<?php if ($post['Post']['modified'] > $post['Post']['created']): ?>
				[Updated: <?=date('j M y h:ia', strtotime($post['Post']['modified'])); ?>]
				<?php endif; ?>
		</footer>
	</article>

<?php endforeach; ?>
<a href="/posts/" class="button tiny">ALL NEWS</a>