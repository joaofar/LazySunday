<script>
    $(document).ready(function() {
        $('.sparktristate').sparkline('html', {type: 'tristate'});
    });
</script>
<?php $data = $this->requestAction('Players/sidebarStats'); ?>

<div class=sideTitle>game stats:</div>
<div class=sideContent>
    <table class="sidebar">
        <tr>
            <td>nº jogos: </td>
            <td><?php echo $data['nGames']; ?></td>
        </tr>
        <tr>
            <td>nº golos: </td>
            <td><?php echo $data['allGoals']; ?></td>
        </tr>
    </table>


</div>

<div class=sideTitle>rating: (min <?php echo $data['n_min_pre']; ?> presenças)</div>
<!--<div class=sideHeuristica>(vitorias/presencas)</div>-->
<div class=sideContent>
    <table class="sidebar">
        <?php
        $i = 1;
        foreach ($data['trueSkill'] as $player): ?>
            <tr>
                <td class="num"><?php echo $i++; ?>º</td>
                <td class="player"><?php echo $this->Html->link(__($player['name']), array('controller' => 'Players', 'action' => 'view', $player['id'])); ?></td>
                <td class="rank"><?php echo round($player['mean'], 1); ?></td>
                <td>
                    <span class="sparktristate"><?php echo $player['tristate'] ?></span>
                </td>
            </tr>
            <?php endforeach; ?>

    </table>
</div>

<div class=sideTitle>player stats:</div>
<div class=sideContent>
    <table class="sidebar">
        <tr>
            <td>golos p/j: </td>
            <td><?php echo $data['topGoalscorer']['Player']['name']; ?>

                (<?php echo $data['topGoalscorer']['Player']['goals_average']; ?>)</td>
        </tr>
        <tr>
            <td>assist p/j: </td>
            <td><?php echo $data['topAssists']['Player']['name']; ?>

                (<?php echo $data['topAssists']['Player']['assists_average']; ?>)</td>
        </tr>
        <tr>
            <td>EM p/j: </td>
            <td><?php echo $data['offensiveInfluence']['Player']['name']; ?>
                (<?php echo $data['offensiveInfluence']['Player']['team_scored_average']; ?>)</td>
        </tr>
        <tr>
            <td>ES p/j: </td>
            <td><?php echo $data['defensiveInfluence']['Player']['name']; ?>
                (<?php echo $data['defensiveInfluence']['Player']['team_conceded_average']; ?>)</td>
        </tr>
    </table>


</div>

