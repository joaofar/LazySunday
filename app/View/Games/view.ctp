

<div id="gameViewTime">
    <table>
        <tr>
<!--            <td>Jogo nº--><?php //echo $n_games;?><!-- / id: (--><?php //echo $id;?><!--)</td>-->
            <td>id: (<?php echo $id;?>)</td>
            <td><?php  echo $this->Time->format('[H:i]  D, d M Y ', $game['Game']['data']); ?></td>
        </tr>
    </table>
</div>


    <!-- JOGO TERMINADO -->
<?php if($game['Game']['estado'] == 2): ?>
    <div class=teams>
        <?php for($i = 0; $i <= 1; $i++): ?>
            <!-- Team  -->
            <div class="teamContainer">

            <!-- HEADER -->
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

            <!-- CONTENT -->
            <div>
                <table>
                    <?php foreach($details[$i]['Player'] as $id => $player): ?>
                    <tr>
                        <td class="shirticon"><?php echo $this->Html->image('small_shirt_'.$i.'.png'); ?></td>
                        <td class="smalltext"style="text-align: right"><?php echo round($player['previousRating'], 1); ?>rt</td>
                        <td class="smalltext" style="text-align: left"><?php  ?></td>
                        <td class="nomejogador"><?php echo $this->Html->link(__($player['name']), array('controller' => 'Players', 'action' => 'view', $id)); ?></td>
                        <td class="goalsAssists" style="text-align: right">
                            <?php
                                // se se gravar apenas os golos das equipas, isto dá erro ao ver o jogo
                                // é portanto preciso filtrar os casos onde não há golos
                                if (isset($player['goals'])) {
                                    echo $player['goals']."(".$player['assists'].")"; 
                                }
                            ?></td>

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


    
