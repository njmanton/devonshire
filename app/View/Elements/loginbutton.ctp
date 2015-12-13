<div class="btn-group">
  <button class="btn btn-success btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
   Logged in as: <strong><?php echo strtoupper($user['username']); ?></strong>
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
  	<li><a href="/users/update"><i class="icon-pencil"></i> Update my details</a></li>
  	<li><a href="/money/<?php echo $user['id']; ?>"><i class="icon-shopping-cart"></i> My Money [<?php echo sterling($user['balance']); ?>]</a></li>
    <?php if ($user['admin']): ?>
    <li class="divider"></li>
    <li><a href="/users/add"><i class="icon-plus-sign"></i> Add New Player</a></li>
    <li><a href="/teams/add"><i class="icon-plus-sign"></i> Add New Team</a></li>
    <li><a href="/competitions/add"><i class="icon-plus-sign"></i> Add New Competition</a></li>
    <li><a href="/ledgers/edit"><i class="icon-wrench"></i> Manage Transactions</a></li>
    <li class="divider"></li>
    <li><a href="/users/send"><i class="icon-envelope"></i> Send bulk email</a></li>
    <?php endif; ?>
  	<li class="divider"></li>
  	<li><a href="/users/logout"><i class="icon-off"></i> Logout</a></li>
  </ul>
</div>