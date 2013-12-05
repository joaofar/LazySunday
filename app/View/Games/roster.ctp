
<div class="teams">
<?php for($i = 0; $i <= 3; $i++): ?>

    <?php
        switch ($i) {
            case 0: $list = $generatedTeams['teams'][$i]['Player']; 
                $header = 'Vermelhos (' . round($generatedTeams['teams'][$i]['Team']['rating'], 1) . ')'; 
                break;
            case 1: $list = $generatedTeams['teams'][$i]['Player']; 
                $header = 'Pretos (' . round($generatedTeams['teams'][$i]['Team']['rating'], 1) . ')'; 
                break;
            case 2: $list = $generatedTeams['bench'];
                $header = 'Banco';
                break;
            case 3: $list = $generatedTeams['out'];
                $header = 'Out'; 
                break;
        }
    ?>

    <?php if(isset($list)): ?>
    <div class="teamContainer">
        <div class="equipa_res">
            <table>
            <tr>
            <td class="convPts"><?php echo $header; ?></td>
            <td class="teamPoints"> </td>
            </tr>
            </table>
        </div>

        <div class="equipa">
            <table>
                <?php if($list != null): ?>
                <?php foreach($list as $key => $player):?>
                    
                    <!-- tornar a row cinzenta se o jogador não tiver respondido -->
                    <tr id=<?php if($player['available'] != 1) { echo 'escuros_null'; } ?>>

                        <!-- icone available (cizento ou verde) -->
                        <td><?php if($player['available'] != 1) { echo $this->Html->image('null.png'); }
                            else{ echo $this->Html->image('ok.png'); } ?></td>
                        <!-- icone t-shirt (preto ou vermelho) -->
                        <td class="shirticon"><?php echo $this->Html->image('small_shirt_'.$i.'.png'); ?></td>
                        <!-- nº -->
                        <td class="num"><?php echo ($key + 1); ?>º</td>
                        <!-- nome do jogador -->
                        <td class="nomejogador"><?php echo $player['name']; ?></td>
                        <!-- rating -->
                        <td><?php echo round($player['mean'], 1); ?> rt</td>
                        <!-- OK/NA postButton, cria uma form com hidden input, daí a form não ter sido declarada -->
                        <?php if (isset($player['invite_id'])):?>
                            <td><?php echo $this->Form->postButton(
                                'OK',
                                array('controller' => 'Invites', 'action' => 'update', 1),
                                array('data' => array(
                                    'Invite.id' => $player['invite_id'],
                                    'Game.id' => $game['Game']['id']))
                                ); ?></td>
                            <td><?php echo $this->Form->postButton(
                                'NA', 
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
    </div>
    <?php endif; ?>

<?php endfor; ?>
</div>
