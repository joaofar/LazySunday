<div>
    <h1>Convocatória</h1>
</div>

<div class="teamsContainer">
<?php for($i = 0; $i <= 3; $i++): ?>

    <?php
        switch ($i) {
            case 0: $list = $generatedTeams['teams'][$i]['Player']; 
                $teamRating = round($generatedTeams['teams'][$i]['Team']['rating'], 1);
                $teamName = 'Pretos';
                $className = 'pretos';
                break;
            case 1: $list = $generatedTeams['teams'][$i]['Player']; 
                $teamRating = round($generatedTeams['teams'][$i]['Team']['rating'], 1);
                $teamName = 'Vermelhos';
                $className = 'vermelhos';
                break;
            case 2: $list = $generatedTeams['bench'];
                $teamRating = '';
                $teamName = 'Banco';
                $className = 'banco';
                break;
            case 3: $list = $generatedTeams['out'];
                $teamRating = '';
                $teamName = 'Out';
                $className = 'out';
                break;
        }
    ?>

    <?php if(isset($list)): ?>
        
            <!-- <div class="teamHeader">
                <table>
                <tr>
                <td class="convPts"><?php echo $header; ?></td>
                <td class="teamPoints"> </td>
                </tr>
                </table>
            </div> -->
            
            <div class="team">
                
                <table>
                <caption>
                <ul>
                    <li><?php echo $this->Html->image('small_shirt_'.$i.'.png') ?></li>
                    <li><?php echo $teamName; ?></li>
                    <li><?php echo $teamRating; ?></li>
                </ul>
                </caption>
                <!-- <thead>
                    <tr>
                        <th class=<?php echo $className; ?>>&nbsp;</th>
                        <th class="teamName"><?php echo $teamName; ?></th>
                        <th class="teamRating"><?php echo $teamRating; ?></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead> -->
                    <?php if($list != null): ?>
                    <?php foreach($list as $key => $player):?>
                        
                        <!-- tornar a row cinzenta se o jogador não tiver respondido -->
                        <tr class=<?php if($player['available'] != 1) { echo 'null'; } ?>>

                            
                            <!-- nº -->
                            <td class=<?php if($player['available'] == 1) { echo $className; } else { echo "null"; } ?>><span><?php echo ($key + 1); ?>º</span></td>
                            <!-- nome do jogador -->
                            <td class="name"><?php echo $this->Html->link($player['name'], array('controller' => 'Players', 'action' => 'view', $player['id'])); ?></td>
                            <!-- rating -->
                            <td class="rating orange"><?php echo round($player['mean'], 1); ?></td>
                            <!-- OK/NA postButton, cria uma form com hidden input, daí a form não ter sido declarada -->
                            <?php if (isset($player['invite_id'])):?>
                                <td class="OK button"><?php echo $this->Form->postButton(
                                    'Ok',
                                    array('controller' => 'Invites', 'action' => 'update', 1),
                                    array('data' => array(
                                        'Invite.id' => $player['invite_id'],
                                        'Game.id' => $game['Game']['id']))
                                    ); ?></td>
                                <td class="NA button"><?php echo $this->Form->postButton(
                                    'Na', 
                                    array('controller' => 'Invites', 'action' => 'update', 0),
                                    array('data' => array(
                                        'Invite.id' => $player['invite_id'],
                                        'Game.id' => $game['Game']['id']))
                                    ); ?></td>
                            <?php endif; ?>
                        </tr>

                    <?php endforeach; ?>
                    <?php endif; ?>

                </table>
            </div>
        
    
    <?php endif; ?>

<?php endfor; ?>
</div>
