<?php   echo $this->Form->Create('Invite', array('action' => 'updateInvites', $id)); ?>
    <div class="teams">
        <?php for($i = 0; $i <= 3; $i++): ?>
            <?php
                switch ($i) {
                    case 0: $list = $generatedTeams['teams'][$i]['Player']; $header = round($generatedTeams['teams'][$i]['Team']['rating'], 1); break;
                    case 1: $list = $generatedTeams['teams'][$i]['Player']; $header = round($generatedTeams['teams'][$i]['Team']['rating'], 1); break;
                    case 2: $list = $generatedTeams['bench'];               $header = 'Banco'                                                 ; break;
                    case 3: $list = $generatedTeams['out'];                 $header = 'Out'                                                   ; break;

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
                                    <td class="num"><?php echo ($key + 1); ?>º</td>

                                    <td class="nomejogador"><?php echo $player['name']; ?></td>
                                    <td><?php echo round($player['mean'], 1); ?> rt</td>
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
