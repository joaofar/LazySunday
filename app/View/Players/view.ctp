<?php

$mean = array_reverse($mean, true);
$standardDeviation = array_reverse($standardDeviation, true);// $difEvo = array_reverse($difEvo, true);
$winLoseStats = array_slice($winLoseStats, 0, 20, true);
$winLoseStats = array_reverse($winLoseStats, true);
$goalsAssists = array_reverse($goalsAssists, true);
//debug($goalsAssists);
//debug($goals);

//debug($playerEvo);
?>

<!-- GRAFICO 1 RATING EVO -->
<script>
    var chart1; // globally available
    $(document).ready(function() {
        chart1 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph',
                height: '300'
                // backgroundColor:'rgba(255, 255, 255, 0.1)'
                //width: '730'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'rating'
            },
            subtitle: {
                text: '(evolução nos últimos jogos)'
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
                    text: 'mean',
                    style: {
                        color: 'red'
                    }
                }
                }
            ],
            xAxis: {
                categories: [<?php foreach($mean as $gameId => $value) { echo($gameId); echo ', '; } ?>]
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
            series: [
                {
                    name: 'mean',
                    type: 'spline',
                    data: [<?php foreach($mean as $value) { echo(round($value, 2)); echo ', '; } ?>]
                },
            ]
        });
    });
</script>

<!-- GRAFICO 5 STANDARD DEVIATION -->
<script>
    var chart1; // globally available
    $(document).ready(function() {
        chart5 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph5',
                height: '300'
                //width: '730'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'grau de incerteza do rating'
            },
            subtitle: {
                text: '(um valor mais baixo é melhor)'
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
                    text: 'mean',
                    style: {
                        color: 'red'
                    }
                }
                }
            ],
            xAxis: {
                categories: [<?php foreach($standardDeviation as $gameId => $value) { echo($gameId); echo ', '; } ?>]
            },
            plotOptions: {
                spline: {
                    dataLabels: {
                        enabled: true,
                        color: 'black'
                    },
                    lineWidth: 4,
                    // color: 'red',
                    marker: {
                        enabled: true
                    }
                },


            },
            legend: {
                enabled: false
            },
            series: [
                {
                    name: 'mean',
                    type: 'spline',
                    data: [<?php foreach($standardDeviation as $value) { echo(round($value, 2)); echo ', '; } ?>]
                },
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
                text: 'diferencial'
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
                categories: [<?php foreach($difEvo as $gameId => $data) { echo($gameId); echo ', '; } ?>]
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
                data: [<?php foreach($difEvo as $data) { echo($data); echo ', '; } ?>]}
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
                text: 'diferença de golos'
            },
            subtitle: {
                text: '(A tua equipa ganhou ou perdeu por... )'
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
                text: 'golos e assistências'
            },
            subtitle: {
                text: '(valores negativos referem-se a jogos nos quais não houve registo de golos/assist)'
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
                categories: [<?php foreach($goalsAssists as $key => $games) { echo $key; echo ', '; } ?>]
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
                data: [<?php foreach($goalsAssists as $game) { echo $game['Goals']; echo ', '; } ?>]
            },
                {
                    name: 'assistências',
                    type: 'column',
                    yAxis: 0,
                    data: [<?php foreach($goalsAssists as $game) { echo $game['Assists']; echo ', '; } ?>]
                }]
        });
    });
</script>

    <div><h1><?php echo $player['Player']['name']; ?></h1></div>

    <div id="pgraph" class="playerGraph">
        <p>pgrapgh</p>
        <?php // echo $this->Html->script('highcharts'); ?>
    </div>


    <div id="pgraph5" class="playerGraph">
        <p>pgraph2</p>
        <?php // echo $this->Html->script('highcharts'); ?>
    </div>

    <div id="pgraph3" class="playerGraph">
        <p>pgraph3</p>
        <?php // echo $this->Html->script('highcharts'); ?>
    </div>

    <div id="pgraph4" class="playerGraph">
        <p>pgraph4</p>
        <?php // echo $this->Html->script('highcharts'); ?>
    </div>


