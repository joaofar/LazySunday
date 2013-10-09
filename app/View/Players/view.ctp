<?php
//debug($allPlayers);
$playerEvo = array_reverse($playerEvo, true);
?>


<script>
    var chart1; // globally available
    $(document).ready(function() {
        chart1 = new Highcharts.Chart({
            chart: {
                renderTo: 'pgraph'
                //height: '600',
                //width: '730'
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
                        color: 'black'
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
                renderTo: 'pgraph2',
                polar: true,
                type: 'line'
            },

            title: {
                text: 'dados do jogador',
                x: -80
            },

            pane: {
                size: '80%'
            },

            xAxis: {
                categories: ['presenças', 'equipa marcados p/jogo', 'equipa sofridos p/jogo', '% de vitórias', 'assistencias p/ jogo', 'golos p/ jogo'],
                tickmarkPlacement: 'on',
                lineWidth: 0
            },

            yAxis: {
                gridLineInterpolation: 'polygon',
                lineWidth: 0,
                min: 0
            },

         //   tooltip: {
         //       shared: true,
         //       pointFormat: '<span style="color:{series.color}">{series.name}: <b>${point.y:,.0f}</b><br/>'
         //   },

            legend: {
                align: 'right',
                verticalAlign: 'top',
                y: 70,
                layout: 'vertical'
            },

            series: [
            <?php foreach($allPlayers as $player) {echo
            "{
                name: '".$player['Player']['nome']."',
                data: [".$player['Player']['equipa_m_p_jogo'].', '
                        .$player['Player']['equipa_m_p_jogo'].', '
                        .$player['Player']['equipa_s_p_jogo'].', '
                        .$player['Player']['vit_pre'].', '
                        .$player['Player']['assist_p_jogo'].', '
                        .$player['Player']['golos_p_jogo']."],
                visible: false,
                pointPlacement: 'on'
                },"; } ?>
            {
                name: 'Allocated Budget',
                data: [0, 0, 0, 0, 0, 0],
                visible: true,
                pointPlacement: 'on'
            }
            ]
        });
    });
</script>

<!--<div class="players view">
<h2><?php /* echo __($player['Player']['nome']);*/?></h2>-->

    <div id="pgraph2" class="playerGraph">
        <p>pgrapgh</p>
        <?php echo $this->Html->script('highcharts'); ?>
    </div>

    <div id="pgraph" class="playerGraph">
        <p>pgraph2</p>
        <?php echo $this->Html->script('highcharts'); ?>
    </div>

