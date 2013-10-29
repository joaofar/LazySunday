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
                name: '%',
                data: [<?php foreach($stats as $y){echo ($y*100).",";} ?>]
            }]
        });
    });
</script>

<div id="pgraph" class="playerGraph">
    <?php echo $this->Html->script('highcharts'); ?>
</div>

<?php debug($stats); ?>