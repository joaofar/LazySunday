<?php
//debug($goals);
$playerEvo = array_reverse($playerEvo, true);
$winLoseStats = array_slice($winLoseStats, 0, 20, true);
$winLoseStats = array_reverse($winLoseStats, true);
$goals = array_reverse($goals, true);
?>

<!-- GRAFICO 1 RATING EVO -->
<script>
    var chart1; // globally available
    $(document).ready(function() {
        chart1 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph',
                height: '300'
                //width: '730'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Evolução do Rating'
            },
            yAxis: [{ // Primary yAxis
                labels: {
                    enabled: false,
                    format: '{value} rt',
                    style: {
                        color: 'red'
                    }
                },
                title: {
                    enabled: false,
                    text: 'rating evo',
                    style: {
                        color: 'red'
                    }
                }
            }],
            xAxis: {
                categories: [<?php foreach($playerEvo as $gameId => $data) { echo($gameId); echo ', '; } ?>]
            },
            plotOptions: {
                spline: {
                    dataLabels: {
                        enabled: true,
                        color: 'black'
                    },
                    lineWidth: 4,
                    color: 'red',
                    marker: {
                        enabled: true
                    }
                },


            },
            legend: {
                enabled: false
            },
            series: [{
                name: 'avg',
                type: 'spline',
                data: [<?php foreach($playerEvo as $data) { echo($data['ratEvo']); echo ', '; } ?>]}
            ]
        });
    });
</script>

<!-- GRAFICO 2 PONTOS POR JOGO -->
<script>
    var chart2; // globally available
    $(document).ready(function() {
        chart2 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph2',
                height: '300'
                //width: '730'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Pontos por Jogo'
            },
            yAxis: [{ // Secondary yAxis
                title: {
                    enabled: false,
                    text: 'game pts',
                    style: {
                        color: 'blue'
                    }
                },
                labels: {
                    enabled: false,
                    format: '{value} pts',
                    style: {
                        //color: '#4572A7'
                    }
                },
                opposite: false
            }
            ],
            xAxis: {
                categories: [<?php foreach($playerEvo as $gameId => $data) { echo($gameId); echo ', '; } ?>]
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        color: 'black'
                    },
                    //color: 'green'
                }
            },
            legend: {
                enabled: false
            },
            series: [{
                name: 'game points',
                type: 'column',
                yAxis: 0,
                data: [<?php foreach($playerEvo as $data) { echo($data['gamePts']); echo ', '; } ?>]}
            ]
        });
    });
</script>

<!-- GRAFICO 3 DIFERENÇA DE GOLOS -->
<script>
    var chart3; // globally available
    $(document).ready(function() {
        chart3 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph3',
                height: '300'
                //width: '730'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Diferença de Golos'
            },
            yAxis: [{ // Terciary yAxis
                title: {
                    enabled: false,
                    text: 'golos',
                    style: {
                        color: 'green'
                    }
                },
                labels: {
                    enabled: false,
                    format: '{value} golos',
                    style: {
                        //color: '#4572A7'
                    }
                },
                opposite: false
            }
            ],
            xAxis: {
                categories: [<?php foreach($winLoseStats as $key => $goal_dif) { echo($key); echo ', '; } ?>]
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: true,
                        color: 'black'
                    },
                    color: 'green'
                }


            },
            legend: {
                enabled: false
            },
            series: [{
                    name: 'golos',
                    type: 'column',
                    yAxis: 0,
                    data: [<?php foreach($winLoseStats as $goal_dif) { echo($goal_dif); echo ', '; } ?>]
                }]
        });
    });
</script>


<!-- GRAFICO 4 GOLOS E ASSISTÊNCIAS -->
<script>
    var chart4; // globally available
    $(document).ready(function() {
        chart4 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph4',
                height: '350'
                //width: '730'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Golos e Assistências'
            },
            yAxis: [{ // Primary yAxis
                title: {
                    enabled: false,
                    text: 'golos',
                    style: {
                        color: 'green'
                    }
                },
                labels: {
                    enabled: true,
                    format: '{value}',
                    style: {
                        //color: '#4572A7'
                    }
                },
                opposite: false
            }
            ],
            xAxis: {
                categories: [<?php foreach($goals as $goal) { echo($goal['Goal']['game_id']); echo ', '; } ?>]
            },
            plotOptions: {
                column: {
                    dataLabels: {
                        enabled: false,
                        color: 'black'
                    },
                    /*color: 'purple'*/
                }


            },
            legend: {
                enabled: true
            },
            series: [{
                name: 'golos',
                type: 'column',
                yAxis: 0,
                data: [<?php foreach($goals as $goal) { echo($goal['Goal']['golos']); echo ', '; } ?>]
            },
                {
                    name: 'assistências',
                    type: 'column',
                    yAxis: 0,
                    data: [<?php foreach($goals as $goal) { echo($goal['Goal']['assistencias']); echo ', '; } ?>]
                }]
        });
    });
</script>



    <div id="pgraph" class="playerGraph">
        <p>pgrapgh</p>
        <?php echo $this->Html->script('highcharts'); ?>
    </div>


    <div id="pgraph2" class="playerGraph">
        <p>pgraph2</p>
        <?php echo $this->Html->script('highcharts'); ?>
    </div>

    <div id="pgraph3" class="playerGraph">
        <p>pgraph3</p>
        <?php echo $this->Html->script('highcharts'); ?>
    </div>

    <div id="pgraph4" class="playerGraph">
        <p>pgraph4</p>
        <?php echo $this->Html->script('highcharts'); ?>
    </div>


