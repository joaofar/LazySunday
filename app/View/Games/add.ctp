<div class="games form">
<?php echo $this->Form->create();?>
	<fieldset>
		<legend><?php echo __('Add Game'); ?></legend>
	<?php
        echo $date = $this->Time->format('Y-m-d', time());
		echo $this->Form->input('Game.date', array('selected' => $date.' 18:30:00'));
        ?>
        <?php echo 'Jogadores a convidar:'; ?>
		<table>
            <?php $i=1; ?>
            <?php foreach($players as $key => $player): ?>
            <tr>
                <td><?php echo $i; ?></td>
                    <?php
                        if($i <= 10){$value = true;}
                        else {$value = false;}
                    ?>
                    
                    <?php echo $this->Form->hidden('Invite.'.$key.'.player_id', array('value' => $player['Player']['id'])); ?>
                <td width="20"><?php echo $this->Form->checkbox('Invite.'.$key.".value", array('checked' => $value)); ?></td>
                <td><?php echo $player['Player']['name']; ?></td>
            </tr>
            <?php $i++; ?>
            <?php endforeach; ?>
        </table>

	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
