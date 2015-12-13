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

<html lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Goalmine</title>
		<?php if (Configure::read('debug') == 0) { ?>
			<meta http-equiv="Refresh" content="<?php echo $pause; ?>;url=<?php echo $url; ?>"/>
		<?php } ?>
		<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="/css/main.css" />
		<?php	echo $this->Html->script(['jquery-1.8.2.min', 'bootstrap.min']); ?>
		
	</head>

	<body>
		<?php if (isset($url) && isset($message)) { ?>
			<a href="<?php echo $url; ?>"><?php echo $message; ?></a>
		<?php } ?>
		<?php echo $this->fetch('content'); ?>
		<?php echo $this->element('ga'); ?>
	</body>
	
</html>
		
