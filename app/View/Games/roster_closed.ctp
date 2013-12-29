<div class="teamsContainer">
	<?php foreach ($teams as $t_key => $team) : ?>
		<div class="team">
			<table>
				<caption>equipa <?php echo $team['Team']['value'] ?></caption>
				<?php foreach ($team['Player'] as $p_key => $player) : ?>
					<tr>
						<td><?php echo $player['name']; ?></td>
						<td class="orange"><?php echo round($player['Rating'][0]['mean'], 1); ?></td>
						<td><?php echo $this->Form->postButton(
							'change team',
							array('action' => $this->params['action'], $this->params['pass'][0]),
							array('data' => array('Player.id' => $player['id'], 'Team.id' => $team['Team']['id']))); ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>
	<?php endforeach; ?>
	<?php echo $this->Form->end(); ?>
</div>

<?php // echo debug($teams); ?>