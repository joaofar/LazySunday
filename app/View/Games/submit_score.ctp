<div><h1>submit score</h1></div>

<div>
    
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