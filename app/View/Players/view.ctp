<?php
foreach($playerEvo as $evo) {
    //debug($evo);
}
?>
<script>
    var chart1; // globally available
    $(document).ready(function() {
        chart1 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph',
                type: 'line',
                height: '650',
                width: '730'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Evolução vit/pre'
            },
            yAxis: {
                title: {
                    text: 'vit/pre'
                },
                min: 200,
                max: 900,
                tickInterval: 100
            },
            xAxis: {
                categories: []
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: false
                    }
                }
            },
            series: [{
                name: '<?php  echo __($player['Player']['nome']);?>',
                data: [<?php foreach($playerEvo as $evo) { echo($evo['Player']['rating']); echo ', '; } ?>]
            }]
        });
    });
</script>

<!--<div class="players view">
<h2><?php /* echo __($player['Player']['nome']);*/?></h2>-->

    <div id="pgraph" class="playerGraph">
        <?php echo $this->Html->script('highcharts'); ?>
    </div>