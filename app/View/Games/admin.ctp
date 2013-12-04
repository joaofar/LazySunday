<div class="admin_sidebar">
    <h2>Admin</h2>
    <ul>
        <li><?php echo $this->Html->link(__('Back to View'), array('action' => 'view', $game['Game']['id'])); ?></li>


        <li><?php echo $this->Html->link(__('Folha de Jogo'), array('action' => 'gs', $game['Game']['id'])); ?></li>

        <?php if($game['Game']['stage'] == 'roster'): ?>
            <li><?php echo $this->Form->postLink('Gravar Equipas','/Players/saveTeams/'.$game['Game']['id']); ?></li>
            <li><?php echo $this->Form->postLink('Enviar Emails','/invites/sendEmails/'.$game['Game']['id']); ?></li>
        <?php endif; ?>
        <li>---</li>
        <li><?php echo $this->Html->link(__('Update Pl Stats'), array('controller' => 'Players', 'action' => 'stats')); ?></li>
        <li>---</li>
        <li><?php echo $this->Html->link(__('Create Team_ids in Goal Col'), array('controller' => 'Games', 'action' => 'teamIdtoGoal')); ?></li>
        <li><?php echo $this->Html->link(__('Generate the Louie rating for each game'), array('controller' => 'Games', 'action' => 'playerPoints_allGames')); ?></li>
        <li><?php echo $this->Html->link(__('Calculate the average for players table'), array('controller' => 'Players', 'action' => 'allAverageRating')); ?></li>
    </ul>

</div>

<?php if($game['Game']['stage'] == 'roster'): ?>
    <div class="notinvited">
        <h2><?php  echo __('Bench');?></h2>
        <table>
            <tr>
                <th>Convidar</th>
                <th>Jogador</th>
            </tr>
            <?php echo $this->Form->Create(null, array('url' => '/Invites/addInvites/'.$game['Game']['id'])); ?>
            <?php foreach($notinvited as $key => $player): ?>

            <tr>
                <td width="20"><?php echo $this->Form->checkbox('jogador'.$key); ?></td>
                <td><?php echo $player; ?></td>
            </tr>
            <?php endforeach; ?>
            <tr><td><?php echo $this->Form->end('Go!'); ?></td><td></td></tr>
        </table>
    </div>
<?php endif; ?>

<?php if($game['Game']['stage'] == 'roster_closed'): ?>
    <div class="submit_goals">
        
        <!-- FORM / SUBMIT SCORE ONLY -->
        <?php echo $this->Form->Create('Team', array(
        'url' => array('controller' => 'Games', 'action' => 'submitScore', $game['Game']['id']))); ?>
        <table>
            <?php echo $this->Form->hidden('0.id', array('value' => $teams[0]['Team']['id'])); ?>
            <?php echo $this->Form->hidden('1.id', array('value' => $teams[1]['Team']['id'])); ?>
            <tr>
                <td><?php echo $this->Form->input('0.score'); ?></td>
                <td><?php echo $this->Form->input('1.score'); ?></td>
            </tr>
        </table>

        <?php echo $this->Form->End('Submit Score'); ?>
        

        <!-- FORM / SUBMIT GOALS -->
        <?php echo $this->Form->Create('Goal', array(
            'url' => array('controller' => 'Games', 'action' => 'submitScore', $game['Game']['id']),
            'inputDefaults' => array('label' => false)
            )); ?>

            <?php $j=0; ?>
            <?php for($i=0; $i <= 1; $i++):?>
                <div class="adminTeam">
                <table>
                    <tr>
                        <th></th>
                        <th>Golos</th>
                        <th>Assist.</th>
                    </tr>
                    
                    
                    <?php foreach($teams[$i]['Player'] as $player): ?>
                    
                    <?php echo $this->Form->hidden($j.'.game_id', array('value' => $game['Game']['id'])); ?>
                    <?php echo $this->Form->hidden($j.'.player_id', array('value' => $player['id'])); ?>
                    <?php echo $this->Form->hidden($j.'.team_id', array('value' => $teams[$i]['Team']['id'])); ?>
                        <tr>
                            <td><?php echo $player['name']; ?></td>
                            <td><?php echo $this->Form->input($j.'.goals'); ?></td>
                            <td><?php echo $this->Form->input($j.'.assists'); ?></td>
                        </tr>
                    <?php $j++; ?> 
                    <?php endforeach; ?>
                </table>
                </div>
            <?php endfor; ?>
        <?php echo $this->Form->end('Submit Goals'); ?>

    </div>
<?php endif; ?>