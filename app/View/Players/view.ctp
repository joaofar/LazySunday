<?php
$playerEvo = array_reverse($playerEvo, true);
?>


<script>
    var chart1; // globally available
    $(document).ready(function() {
        chart1 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph',
               // type: 'spline',
                height: '650',
                width: '730'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'RatingLouie'
            },
            yAxis: [{ // Primary yAxis
                labels: {
                    //format: '{value} pts',
                    style: {
                        color: 'red'
                    }
                },
                title: {
                    text: 'rating evo',
                    style: {
                        color: 'red'
                    }
                }
            }, { // Secondary yAxis
                title: {
                    text: 'game pts',
                    style: {
                        color: '#4572A7'
                    }
                },
                labels: {
                    format: '{value} pts',
                    style: {
                        //color: '#4572A7'
                    }
                },
                opposite: true
            }],
            xAxis: {
                categories: []
            },
            plotOptions: {
                spline: {
                    dataLabels: {
                        enabled: true,
                        color: '#8A2908'
                    },
                    lineWidth: 4,
                    color: 'red',
                    marker: {enabled: true,
                                color: 'black'}
                },
                column: {
                    dataLabels: {
                        enabled: false
                    },
                    color: '#BDBDBD'
                }


            },
            series: [{
                name: 'game points',
                type: 'column',
                yAxis: 1,
                data: [<?php foreach($playerEvo as $key => $evo) { echo($key); echo ', '; } ?>]},

                {
                name: 'avg',
                type: 'spline',
                //yAxis: 2,
                data: [<?php foreach($playerEvo as $evo) { echo($evo); echo ', '; } ?>]
            }]
        });
    });
</script>

<script>
    var chart2; // globally available
    $(document).ready(function() {
        chart2 = new Highcharts.Chart({
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
                text: 'Evolução do Ranking'
            },
            yAxis: {
                title: {
                    text: 'Ranking'
                },
                min: 100,
                max: 1000,
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
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [
                <?php foreach($players as $id => $player):?>
                {
                    name: '<?php  echo $id; ?>',
                    data: [<?php foreach($player as $game) { echo($game); echo ', '; } ?>]
                },
                <?php endforeach; ?>
            ]
        });
    });
</script>

<!--<div class="players view">
<h2><?php /* echo __($player['Player']['nome']);*/?></h2>-->

    <div id="pgraph2" class="playerGraph">
        <?php echo $this->Html->script('highcharts'); ?>
    </div>

    <div id="pgraph" class="playerGraph">
        <?php echo $this->Html->script('highcharts'); ?>
    </div>