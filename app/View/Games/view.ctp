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
        <?php for($i = 1; $i <= 2; $i++): ?>
            <!--- Team  --->
            <div class="teamContainer">

                    <!--- HEADER --->
                    <div class="equipa_res">
                        <table>
                            <tr>
                                <td class="score">[<?php echo ${'team_'.$i.'_score'}; ?>]</td>
                                <td class="teamPoints">
                                    <?php
                                        $teamPoints = 0;
                                        foreach(${'team_'.$i.'_data'} as $data){
                                            $teamPoints += $data['curr_rating'];
                                                }
                                    echo $teamPoints;
                                    ?>pts
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!--- CONTENT --->
                    <div>
                        <table>
                            <?php foreach(${'team_'.$i.'_data'} as $nomejogador => $data): ?>
                            <tr>
                                <td class="shirticon"><?php echo $this->Html->image('small_shirt_'.$i.'.png'); ?></td>
                                <td class="smalltext"style="text-align: right"><?php echo $data['curr_rating']; ?>rt</td>
                                <td class="smalltext" style="text-align: left">(<?php echo $data['peso']; ?>%)</td>
                                <td class="nomejogador"><?php echo $this->Html->link(__($nomejogador), array('controller' => 'Players', 'action' => 'view', $data['id'])); ?></td>
                                <td style="text-align: right"><?php echo $data['golos']."(".$data['assistencias'].")"; ?></td>

                                <td style="text-align: right"><?php echo $data['player_points']; ?>pts</td>
                                <td class="smalltext" style="text-align: right">(<?php echo $data['basePts']; ?> +</td>
                                <td class="smalltext" style="text-align: left"><?php echo $data['spPts']; ?>)</td>
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
                                        <td class="shirticon"><?php echo $this->Html->image('small_shirt_'.$i.'.png'); ?></td>
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





<!-----------------INVITES----------------------->
<?php if($game['Game']['estado'] == 5): ?>
    <div id="invbase">
        <?php foreach($invites as $invite): ?>
        <?php   echo $this->Form->Create('Invite', array('action' => 'updateInvites/'.$game['Game']['id'])); ?>
        <?php
            $answered = !is_null($invite['Invite']['available']);
            $valor = false;
            if($answered) {
                if($invite['Invite']['available']) $valor = true;
            } else {
                $valor = null;
            }
?>
        <div class="box">
          <div class="state" style="<?php
              if($valor) echo 'background-color: #00FF00';
              elseif(is_null($valor)) echo 'background-color: #c3c3c3';
              else echo 'background-color: #FF0000';
              ?>"></div>
          <div class="rating"><div class="ratingvalor" style="<?php echo 'width:'.$invite['Player']['rating']*0.140;echo 'px'; ?>"></div></div>
          <div class="rating_n"><?php echo $invite['Player']['rating']; ?></div>
          <div class="player"><?php echo $invite['Player']['nome']; ?></div>
          <div class="presence_off presence_txt"><?php echo $this->Form->button('NA', array('name' => $invite['Player']['id'], 'value' => 0, 'div' => false)); ?></div>
          <div class="presence_on presence_txt"><?php echo $this->Form->button('OK', array('name' => $invite['Player']['id'], 'value' => 1, 'div' => false)); ?></div>
        </div>
        <?php echo $this->Form->end(); ?>
        <?php endforeach; ?>
     </div>
<br/>
<?php endif; ?>

