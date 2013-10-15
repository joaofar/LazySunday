<?php debug($stats); ?>

<?php //$stats = array_slice($stats, 0, 5);
// <?php foreach($stats as $y){echo $y.",";} ?>

<script>
    var chart1; // globally available
    $(document).ready(function() {
        chart1 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph'
            },

            xAxis: {
                categories: [<?php foreach($stats as $x => $y){echo $x.",";} ?>]
            },

            series: [{
                name: 'Tokyo',
                data: [<?php foreach($stats as $y){echo $y.",";} ?>]
            }]
        });
    });
</script>



<!--<div class="players view">
<h2><?php /* echo __($player['Player']['nome']);*/?></h2>-->


<div id="pgraph" class="playerGraph">
    <?php echo $this->Html->script('highcharts'); ?>
</div>