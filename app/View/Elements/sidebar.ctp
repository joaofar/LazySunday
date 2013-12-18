<script>
    $(document).ready(function() {
        $('.sparktristate').sparkline('html', {type: 'tristate'});
    });
</script>
<?php $data = $this->requestAction('Players/sidebarStats'); ?>

<!-- GAME STATS -->
<div class=stats>
    <table class="sidebar">
        <caption>game stats</caption>

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

<!-- RANKING -->
<div class=ranking>
    <table class="sidebar link">
        <caption>ranking</caption>
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

<!-- PLAYER STATS -->
<div class=stats>
    <table class="sidebar">
        <caption>player stats</caption>
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

