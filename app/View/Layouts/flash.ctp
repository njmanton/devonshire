<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html>

<html class="flash" lang="en">

	<head>
		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $title_for_layout; ?></title>
		<?php if (Configure::read('debug') == 0) { ?>
			<meta http-equiv="Refresh" content="<?php echo $pause; ?>;url=<?php echo $url; ?>"/>
		<?php } ?>
		<link rel="stylesheet" type="text/css" href="/css/master.css" />
		
	</head>

	<body>
		<?php if (isset($message)) { ?>
			<section class="flash-message">
				<?php echo $message; ?>
			</section>
		<?php } ?>
		<?php echo $this->fetch('content'); ?>
		<?php echo $this->element('ga'); ?>
	</body>
	
</html>
		
