<div class="games index">
    <table>
        <tr colspan="4">
            <!--<td><?php /*echo $this->Html->link(__('New Game'), array('action' => 'add')); */?></td>-->
        </tr>
        <?php foreach ($games as $game): ?>
            <tr>
                <td><h1><?php echo $this->Html->link(__('>>>'), array('action' => 'view', $game['Game']['id'])); ?>&nbsp;</h1></td>
                <td><?php echo $this->Time->format('d M, Y', $game['Game']['data']); ?>&nbsp;</td>
                <td><?php echo $game['Game']['team_a_score']." : ".$game['Game']['team_b_score']; ?>&nbsp;</td>
                <td>
                    <?php
                        if($game['Game']['estado'] == 'roster') {
                            echo "Convocatória";
                        }
                        elseif ($game['Game']['estado'] == 'closed') {
                            echo "A decorrer...";
                        }
                        else {
                            echo "Terminado";
                        }

                    ?>
                    &nbsp;</td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
