<div id="gameViewTime">
    <table>
        <tr>
<!--            <td>Jogo nº--><?php //echo $n_games;?><!-- / id: (--><?php //echo $id;?><!--)</td>-->
            <td>id: (<?php echo $id;?>)</td>
            <td><?php  echo $this->Time->format('[H:i]  D, d M Y ', $game['Game']['data']); ?></td>
        </tr>
    </table>
</div>
    <?php //debug($generatedTeams['out']); ?>


    <!--- JOGO TERMINADO --->

<?php if($game['Game']['estado'] == 2): ?>
    <div class=teams>
        <?php for($i = 0; $i <= 1; $i++): ?>
            <!--- Team  --->
            <div class="teamContainer">

            <!--- HEADER --->
            <div class="equipa_res">
                <table>
                    <tr>
                        <td class="score">[<?php echo $details[$i]['Team']['score']; ?>]</td>
                        <td class="teamPoints">
                            <?php ?>
                        </td>
                    </tr>
                </table>
            </div>

            <!--- CONTENT --->
            <div>
                <table>
                    <?php foreach($details[$i]['Player'] as $id => $player): ?>
                    <tr>
                        <td class="shirticon"><?php echo $this->Html->image('small_shirt_'.$i.'.png'); ?></td>
                        <td class="smalltext"style="text-align: right"><?php echo round($player['previousRating'], 1); ?>rt</td>
                        <td class="smalltext" style="text-align: left"><?php  ?></td>
                        <td class="nomejogador"><?php echo $this->Html->link(__($player['name']), array('controller' => 'Players', 'action' => 'view', $id)); ?></td>
                        <td style="text-align: right"><?php echo $player['goals']."(".$player['assists'].")"; ?></td>

                        <td style="text-align: right"><?php echo round($player['currentRating'], 1); ?>pts</td>
                        <td class="smalltext" style="text-align: left">(<?php echo $player['difference']; ?>)</td>
                        <td class="smalltext" style="text-align: left"><?php echo $player['standardDeviation']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            </div>
        <?php endfor; ?>
    </div>
<?php endif; ?>


    <!--- CONVOCATÓRIA --->
    <!--- O código é reusado 4 vezes para gerar quatro listas: 2 x equipas, 1 x banco e 1 x out --->
<?php if($game['Game']['estado'] == 0): ?>
    <?php   echo $this->Form->Create('Invite', array('action' => 'updateInvites/'.$game['Game']['id'])); ?>
        <div class="teams">
            <?php for($i = 1; $i <= 4; $i++): ?>
                <?php
                    switch ($i) {
                        case 1: $list = $generatedTeams['teams']['team_'.$i]; $header = $generatedTeams['teams']['team_'.$i.'_rating']; break;
                        case 2: $list = $generatedTeams['teams']['team_'.$i]; $header = $generatedTeams['teams']['team_'.$i.'_rating']; break;
                        case 3: $list = $generatedTeams['banco'];             $header = 'Banco'                                       ; break;
                        case 4: $list = $generatedTeams['out'];               $header = 'Out'                                         ; break;

                    }
                ?>

                <?php if(isset($list)): ?>
                <div class="teamContainer">
                    <div class="equipa_res">
                        <table>
                            <tr>
                                <td class="convPts">[<?php echo $header; ?>]</td>
                                <td class="teamPoints"> </td>
                            </tr>
                        </table>
                    </div>

                    <div class="equipa">
                        <table>
                            <?php if($list != null): ?>
                                <?php foreach($list as $key => $player):?>

                                    <tr id=<?php if($player['available'] != 1) { echo 'escuros_null'; } ?>>
                                        <td><?php
                                            if($player['available'] != 1) { echo $this->Html->image('null.png'); }
                                            else{ echo $this->Html->image('ok.png'); } ?></td>
                                        <td class="shirticon"><?php echo $this->Html->image('small_shirt_'.($i - 1).'.png'); ?></td>
                                        <td class="num"><?php echo $key; ?>º</td>

                                        <td class="nomejogador"><?php echo $player['name']; ?></td>
                                        <td><?php echo $player['rating']; ?> rt</td>
                                        <td><?php echo $this->Form->button('NA', array('name' => $player['id'], 'value' => 0, 'div' => false)); ?></td>
                                        <td><?php echo $this->Form->button('OK', array('name' => $player['id'], 'value' => 1, 'div' => false)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

             <?php endfor; ?>
        </div>
    <?php echo $this->Form->end(); ?>
<?php endif; ?>

