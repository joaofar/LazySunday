
<!-- Convidar jogadores -->
<?php if($game['Game']['stage'] == 'roster'): ?>
    <div class="notinvited">
        <h2><?php  echo __('Bench');?></h2>
        <table>
            <tr>
                <th>Convidar</th>
                <th>Jogador</th>
            </tr>
            <?php echo $this->Form->Create('Player', array('url' => array(
                'controller' => 'Invites',
                'action' => 'addInvites',
                $game['Game']['id'])
                )); ?>
            <?php foreach($not_invited as $key => $player): ?>

            <tr>
                <?php echo $this->Form->hidden('Player.'.$key.'.id', array('value' => $player['id'])); ?>
                <td width="20"><?php echo $this->Form->checkbox('Player.'.$key.'.value'); ?></td>
                <td><?php echo $player['name']; ?></td>
                <td><?php echo $player['mean']; ?></td>
            </tr>
            <?php endforeach; ?>
            <tr><td><?php echo $this->Form->end('Go!'); ?></td><td></td></tr>
        </table>
    </div>
<?php endif; ?>

