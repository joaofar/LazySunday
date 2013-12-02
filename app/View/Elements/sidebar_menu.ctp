<ul>
	<?php foreach ($sidebarMenu as $url): ?>
		<li><?php echo $this->Html->link($url['title'], array(
		'controller' => $url['controller'],
		'action' => $url['action'], $url['value'])); ?></li>
	<?php endforeach; ?>	
</ul>
