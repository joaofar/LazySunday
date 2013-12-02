<!DOCTYPE HTML>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo 'LazyFoot' ?>:
        <?php echo $title_for_layout; ?>
    </title>
    <?php
    echo $this->Html->meta('icon');

    echo $this->Html->css('styles');
    ?>
    <script src="<?php echo $this->webroot; ?>js/jquery-1.7.2.min.js" type="text/javascript" ></script> 
    <script src="<?php echo $this->webroot; ?>js/jquery.sparkline.js" type="text/javascript" ></script> <script src="<?php echo $this->webroot; ?>js/highcharts.js" type="text/javascript"></script>
    <script src="<?php echo $this->webroot; ?>js/highcharts-more.js" type="text/javascript"></script>
    <?php echo $scripts_for_layout; ?>
</head>

<body>
<div id="header">
    <div id="headerMenu">
        <div id="topMenu">
            <div id="topMenuLogo">Lazyfoot</div>
        </div>
        <div id="bottomMenu">
            <ul id="menu">
                <li><a href="<?php echo $this->webroot; ?>Games">Jogos</a></li>
                <li><a href="<?php echo $this->webroot; ?>Players/index/20">Jogadores c/ 20pre</a></li>
                <li><a href="<?php echo $this->webroot; ?>Players/index">Jogadores</a></li>
            </ul>
        </div>
    </div>
</div>

<div id="outerWrapper">
    <div id="contentWrapper">
        <div id="noOverlapTop"></div>
        <!-- SIDEBAR -->
        <div id="sidebarWrapper">
            <?php if (isset($sidebarMenu)):?>
            <div id="sidebarContent">
                <h3><?php echo $this->element('sidebar_menu') ?></h3>
            </div>
        <?php endif; ?>
            <div id="sidebarContent">
                <h3><?php echo $this->element('sidebar'); ?></h3>
            </div>
        </div>
        <!-- CONTENT -->
        <div id="contentInnerWrapper">
            <div id="content">
                <?php echo $this->Session->flash(); ?>

                <?php echo $content_for_layout; ?>
            </div>
        </div>
        <div id="noOverlap"></div>
    </div>
</div>
<div id="footerWrapper">
    <div id="footerContent">
        <div id="credits"><u>LazyFoot</u> beta...<?php echo date('F Y'); ?></div>
    </div>
</div>
</body>
</html>
