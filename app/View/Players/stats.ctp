<?php echo $stats; ?>

<script>
    var chart1; // globally available
    $(document).ready(function() {
        chart1 = new Highcharts.Chart({
            chart: {
                type: 'bar',
                renderTo: 'pgraph'
            },
            title: {
                text: 'Tareias Históricas'
            },
            xAxis: {
                categories: ['Apples', 'Oranges', 'Pears', 'Grapes', 'Bananas']
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total fruit consumption'
                }
            },
            legend: {
                backgroundColor: '#FFFFFF',
                reversed: true
            },
            plotOptions: {
                series: {
                    stacking: 'normal'
                }
            },
            series: [{
                name: 'Pretos',
                data: [5, 3, 4, 7, 2]
            }, {
                name: 'Vermelhos',
                data: [2, 2, 3, 2, 1]
            }]
    });
    });
</script>



<!--<div class="players view">
<h2><?php /* echo __($player['Player']['nome']);*/?></h2>-->


<div id="pgraph" class="playerGraph">
    <?php echo $this->Html->script('highcharts'); ?>
</div>


<div id="pgraph2" class="playerGraph">
    <?php echo $this->Html->script('highcharts'); ?>
</div>