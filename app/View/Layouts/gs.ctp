<!DOCTYPE HTML>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <?php
    echo $this->Html->meta('icon');

    echo $this->Html->css('game_sheet');
    ?>
</head>

<body>
<?php foreach ($teams['teams'] as $key => $team) : ?>
    <div class='sheet'>
        <div class='assistencias'></div>
        <div class='golos'></div>

        <div class='gameSheet'>
         <table>
            <tr class='header'>
                <td></td>
                <td>sem assist.</td>
                <?php foreach($team['Player'] as $player) : ?>
                    <td class='playerAssists'><?php echo $player['name']; ?></td>
                <?php endforeach; ?>
            </tr>

            <?php $i = 1; ?>
            <?php foreach($team['Player'] as $player) : ?>
            <tr>
                <td class='playerGoals'><?php echo $player['name']; ?></td>
                <td></td>
                <td><?php if($i==1){ echo "------"; } ?></td>
                <td><?php if($i==2){ echo "------"; } ?></td>
                <td><?php if($i==3){ echo "------"; } ?></td>
                <td><?php if($i==4){ echo "------"; } ?></td>
                <td><?php if($i==5){ echo "------"; } ?></td>
            </tr>
            <?php $i++; endforeach; ?>
        </table>   
        </div>
    </div>
<?php endforeach; ?>
<?php // echo debug($teams); ?>
</body>
</html>