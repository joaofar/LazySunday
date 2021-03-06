<!DOCTYPE HTML>
<html>
<head>
	<meta name="viewport" content="width=device-width">
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo 'lazyfoot' ?>:
        <?php echo $title_for_layout; ?>
    </title>
    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->css('styles');
    ?>

	<!-- SCRIPTS -->
	<?php echo $this->Html->script(array(
		'/app/webroot/bower_components/jquery/jquery.min',
		'/app/webroot/bower_components/jquery.sparkline/jquery.sparkline.min',
		'/app/webroot/bower_components/highcharts/highcharts',
		'lazyfoot')); ?>

	<?php echo $scripts_for_layout; ?>
</head>

<body>

<div id="container">
	<div id="header">
		<div id="logo"><?php echo $this->Html->link('lazyfoot', array('controller' => 'Games', 'action' => 'index')); ?></div>
		<ul id="menu">
	        <li><?php echo $this->Html->link('Jogos', array('controller' => 'Games', 'action' => 'index')); ?></li>
	        <li><?php echo $this->Html->link('Jogadores c/20 pre', array('controller' => 'Players', 'action' => 'index', 20)); ?></li>
	        <li><?php echo $this->Html->link('Jogadores', array('controller' => 'Players', 'action' => 'index')); ?></li>
	    </ul>
	</div>

	<div id="wrapper">
	<div id="navigation">
	<?php echo $this->element('sidebar_menu'); ?>
	<?php echo $this->element('sidebar'); ?></div>

	<div id="content"> <?php echo $this->fetch('content'); ?></div>
	</div>
<div id="footer"><p><u>LazyFoot</u> beta...<?php echo date('F Y'); ?></p></div>
</div>

<?php echo $this->Js->writeBuffer(); ?>
<script src="//0.0.0.0:35729/livereload.js"></script>
</body>


</html>