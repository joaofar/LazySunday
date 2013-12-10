<div class="players index">
<!--	<h2>--><?php //echo __('Jogadores');?><!--</h2>-->
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><h2><?php echo $this->Paginator->sort('name', 'Nome');?></h2></th>
			<th><h2><?php echo $this->Paginator->sort('0.mean', 'Rating'); ?></h2></th>
			<th><h2><?php echo $this->Paginator->sort('games_played', 'Pre');?></h2></th>
			<!-- <th><h2><?php // echo $this->Paginator->sort('rating', 'R');?></h2></th> -->
			<th><h2><?php echo $this->Paginator->sort('wins','V');?></h2></th>
            <th><h2><?php echo $this->Paginator->sort('win_percentage','V/P');?></h2></th>
            <th><h2><?php echo $this->Paginator->sort('goals_average','G/J (Total)');?></h2></th>
            <th><h2><?php echo $this->Paginator->sort('assists_average','A/J (Total)');?></h2></th>
            <th><h2><?php echo $this->Paginator->sort('team_scored_average','EM/J (Total)');?></h2></th>
            <th><h2><?php echo $this->Paginator->sort('team_conceded_average','ES/J (Total)');?></h2></th>
	</tr>
	<?php
	foreach ($players as $player): ?>
	<tr>
		<td><?php echo $this->Html->link(__($player['Player']['name']), array('action' => 'view', $player['Player']['id'])); ?>&nbsp;</td>
		<td><?php echo h(round($player['Rating'][0]['mean'], 1)); ?>&nbsp;</td>
		<td><?php echo h($player['Player']['games_played']); ?>&nbsp;</td>
		<!-- <td><?php // echo h($player['Player']['rating']); ?>&nbsp;</td> -->
		<td><?php echo h($player['Player']['wins']); ?>&nbsp;</td>
        <td><?php echo h($player['Player']['win_percentage']); ?>&nbsp;</td>
        <td><?php echo h($player['Player']['goals_average']); ?> (<?php echo h($player['Player']['goals']); ?>)&nbsp;</td>
        <td><?php echo h($player['Player']['assists_average']); ?> (<?php echo h($player['Player']['assists']); ?>)&nbsp;</td>
        <td><?php echo h($player['Player']['team_scored_average']); ?> (<?php echo h($player['Player']['team_scored']); ?>)&nbsp;</td>
        <td><?php echo h($player['Player']['team_conceded_average']); ?> (<?php echo h($player['Player']['team_conceded']); ?>)&nbsp;</td>

	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ' '));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
