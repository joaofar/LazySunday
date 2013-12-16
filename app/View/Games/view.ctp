

<div class="title">
    <table>
        <tr>
<!--            <td>Jogo nº--><?php //echo $n_games;?><!-- / id: (--><?php //echo $id;?><!--)</td>-->
            <td>id: (<?php echo $id;?>)</td>
            <td><?php  echo $this->Time->format('[H:i]  D, d M Y ', $game['Game']['date']); ?></td>
        </tr>
    </table>
</div>

<div class="content">
<!-- GAME SCORE -->
        <div class="score">
            <ul>
                <li><?php echo $details[0]['Team']['score']; ?></li>
                <li></li>
                <li><?php echo $details[1]['Team']['score']; ?></li>
            </ul>
        </div>
        
        <div class="teamsContainer">
    <?php for($i = 0; $i <= 1; $i++): ?>
        <?php $teamName = ($i == 0) ? 'vermelhos' : 'pretos' ;?>
        <!-- Team  -->
        <div class="team">
        <!-- CONTENT -->
        
            <table>
            <!-- <caption>
                <ul>
                    <li><?php echo $this->Html->image('small-shirt-white.png'); ?></li>
                    <li><?php echo $teamName ?></li>
                </ul>
            </caption> -->
                <?php foreach($details[$i]['Player'] as $id => $player): ?>
                <tr>
                    <!-- shirt icon -->
                    <!-- <td class="shirticon"><?php echo $this->Html->image('small-shirt-white.png'); ?></td> -->
                    <!-- previous rating -->
                    <!-- <td class="smalltext"style="text-align: right"><?php echo round($player['previousRating'], 1); ?>rt</td> -->

                    <!-- difference -->
                    <td class="smalltext" style="text-align: left"><?php echo round($player['difference'], 3); ?></td>
                    <!-- current rating -->
                    <td><?php echo round($player['currentRating'], 1); ?>pts</td>
                    <!-- nome do jogador -->
                    <td><?php echo $this->Html->link(__($player['name']), array('controller' => 'Players', 'action' => 'view', $id)); ?></td>
                    <!-- golos / assistências -->
                    <td class="goalsAssists" style="text-align: right">
                        <?php
                            // se se gravar apenas os golos das equipas, isto dá erro ao ver o jogo
                            // é portanto preciso filtrar os casos onde não há golos
                            if (isset($player['goals'])) {
                                echo $player['goals']; 
                            }
                            if (isset($player['assists'])) {
                                echo "(".$player['assists'].")"; 
                            }
                        ?></td>

                    
                    
                    <!-- <td class="smalltext" style="text-align: left"><?php echo $player['standardDeviation']; ?></td> -->
                </tr>
                <?php endforeach; ?>
            </table>

        </div>
    <?php endfor; ?>
</div>
</div>


    
