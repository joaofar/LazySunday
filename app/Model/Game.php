<?php
App::uses('AppModel', 'Model');

/**
 * Game Model
 *
 * @property Goal $Goal
 * @property Invite $Invite
 * @property Team $Team
 */
class Game extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'data';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'data' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Goal' => array(
			'className' => 'Goal',
			'foreignKey' => 'game_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Invite' => array(
			'className' => 'Invite',
			'foreignKey' => 'game_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'game_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
    public $hasAndBelongsToMany = array(
        'Player' => array(
            'className' => 'Player',
            'joinTable' => 'games_players',
            'foreignKey' => 'game_id',
            'associationForeignKey' => 'player_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );

/**
 * virtual fields
 *
 * @var array
 */

    public $virtualFields = array(
        'goal_dif' => 'Game.team_a - Game.team_b'
    );



/**
 * teamsGoals method
 *
 * @param string $id
 * @return array
 */

    public function teamsGoals($id){

        //encontrar as equipas do jogo
        $game = $this->find('first', array('conditions' => array('id' => $id), 'recursive' => 1));

        //encontrar os dados de cada equipa
        $i = 1;
        foreach($game['Team'] as $team){

            $goaloptions = array('conditions' => array('team_id' => $team['id']), 'recursive' => 1);
            $goals = $this->Goal->find('all', $goaloptions);


            ${'team_'.$i.'_score'} = 0;
            foreach($goals as $goal){

              ${'team_'.$i.'_data'}[$goal['Player']['nome']] = array(
                  'id' => $goal['Goal']['player_id'],
                  'golos' => $goal['Goal']['golos'],
                  'assistencias' => $goal['Goal']['assistencias'],
                  'player_points' => $goal['Goal']['player_points'],
                  'curr_rating' => $goal['Goal']['curr_rating'],
                  'peso' => $goal['Goal']['peso'],
                  'basePts' => $goal['Goal']['basePts'],
                  'spPts' => $goal['Goal']['spPts']
                  );

                ${'team_'.$i.'_score'} += $goal['Goal']['golos'];
            }

            $i++;
        }

        return array(
            'team_1_data' => $team_1_data,
            'team_2_data' => $team_2_data,
            'team_1_score' => $team_1_score,
            'team_2_score' => $team_2_score
            );
    }

/**
 * percentDist() method
 *
 * esta função determina qual a percentagem de pontos que a equipa vencedora recebe dependendo na diferença de golos.
 *
 * @param
 * @return
 */

    public function percentDist() {

        //diferença de golos necessária para se ter 100% dos pontos
        $x = 16;

        //valor base qd a diferença de golos é 0, quer dizer que cada equipa recebe 0.5 (50% dos pontos)
        $c = 0.5;

        //este valor determina a inclinação da curva
        $b = 0.006;
        $a = ((0.55 - $c) - $b*$x) / pow($x, 2);

        //equação quadrática para determinar os pontos
        for($i = 0; $i <= $x; $i++){
            $y[$i] = $a*pow($i,2) + $b*$i + $c;
        }

        //devolve uma array com $x entradas e os respectivos valores entre [0.5 e 1]
        return $y;
    }

/**
 * calcula o player points para todos os jogos
 *
 * @param
 * @return
 */

    public function playerPoints_allGames() {

        //array com todos os jogadores
        $games = $this->find('all');

        foreach($games as $game){

            $game['Game']['id'];
            $this->playerPoints_new($game['Game']['id']);
        }
    }

/**
 * faz o rating de cada jogador no jogo seleccionado
 *
 * @param array $team
 * @return bool
 */

    public function playerPoints($id) {

        // Peso dos golos e assistências no rating final [0 a 1] ////
            $pointsWeight = 0.125;
        // Pontos por jogo
            $pointsPerGame = 5000;
        // Peso dos golos em relação às assistências [0 a 1]
            $goalAssistWeight = 0.618;
        //////////////////////////////////////////////

        $teams = $this->Team->find('all', array('conditions' => array('Team.game_id' => $id), 'recursive' => 1));
        $goalDif = abs($teams[0]['Team']['golos'] - $teams[1]['Team']['golos']);
        $percentDist = $this->percentDist();

        /* loop para cada equipa
           no 1º loop criam-se os pontos base para cada equipa
           no 2º loop fazem-se os pontos para cada jogador.
        */
        $i=0;
        foreach($teams as $team){
            //pontos totais de cada equipa
            //$teamPoints[$i]['Team'] = ($teams[$i]['Team']['golos'] / $totalGoals) * $pointsPerGame;

            //debug($team);
            if($team['Team']['winner'] == 1){
                $teamPoints[$i]['Team'] = $percentDist[$goalDif] * $pointsPerGame;
            }
            else{
                $teamPoints[$i]['Team'] = (1 - $percentDist[$goalDif]) * $pointsPerGame;
            }

            //pontos base, cada jogador recebe pelo menos estes pontos
            $teamPoints[$i]['Base'] = ($teamPoints[$i]['Team'] * (1 - $pointsWeight));

            //pontos a serem distribuidos pelos jogadores que marcaram golos e fizeram assistências
            $teamPoints[$i]['specialPoints'] = $teamPoints[$i]['Team'] * $pointsWeight;

            //total de assistências nesta equipa
            $teams[$i]['Team']['assistencias'] = 0;

            foreach($team['Goal'] as $player){
                $teams[$i]['Team']['assistencias'] += $player['assistencias'];
            }

            //IMPORTANTE -> pontos por cada Golo. Segue a proporção indicada em $goalAssistWeight
            $pointsPerGoal = $teamPoints[$i]['specialPoints'] / ($teams[$i]['Team']['golos'] +
                                                                $teams[$i]['Team']['assistencias'] * $goalAssistWeight);
            //este valor descobre-se usando o ratio
            $pointsPerAssist = $pointsPerGoal * $goalAssistWeight;



          //VALOR DA EQUIPA E RATING DO JOGADOR NA ALTURA DO JOGO
          //é usado para descobrir o peso de cada jogador na equipa no próximo loop
            $teamValue = 0;


          foreach($team['Goal'] as $player){

              //rating deste jogador na altura deste jogo
              //procurar o último jogo do jogador que é o segundo item neste array
              $previousGame = $this->Goal->find('all', array('conditions' => array('Goal.game_id <' => $player['game_id'], 'Goal.player_id' => $player['player_id']),
                  'order' => array('Goal.id' => 'desc'),
                  'limit' => 1));

              //se não existirem jogos, usa-se o rating base
              //se existirem usa-se a função playerPointsAvg para calcular o rating de um jogador para um game_id
              if(count($previousGame) == 0){
                  $playerTable = $this->Player->findById($player['player_id']);
                  $currRating[$player['player_id']] = $playerTable['Player']['ratingBase'];
              }
              else{
                  $currRating[$player['player_id']] = $this->Player->playerPointsAvg($player['player_id'], $previousGame[0]['Goal']['game_id']);
              }
              $teamValue += $currRating[$player['player_id']];
          }


          foreach($team['Goal'] as $player){

              $goalPoints = $player['golos'] * $pointsPerGoal;
              $assistPoints = $player['assistencias'] * $pointsPerAssist;

              //somar os pontos base mais os pontos especiais
              $playerPoints = ($teamPoints[$i]['Base'] * ($currRating[$player['player_id']] / $teamValue)) + ($goalPoints + $assistPoints);

              //$pointsSave = array('Goal' => array('game_id' => $id, 'player_id' => $player['player_id'], 'player_points' => $playerPoints));
              $pointsSave = array('Goal' => array('player_points' => $playerPoints,
                                                  'curr_rating' => $currRating[$player['player_id']],
                                                  'peso' => round(($currRating[$player['player_id']] / $teamValue), 3) * 100,
                                                  'spPts' => $goalPoints + $assistPoints,
                                                  'basePts' => ($teamPoints[$i]['Base'] * ($currRating[$player['player_id']] / $teamValue))));

              //debug($playerPoints);
              $this->Goal->id = $player['id'];
              $this->Goal->save($pointsSave);

              //actualizar o rating para a tabela de jogadores, para este jogador
              $this->Player->averageRating($player['player_id']);

              //debug array
              $debugArray[$player['player_id']] = array('playerPoints' => $playerPoints,
                                                        'peso na equipa' => $currRating[$player['player_id']] / $teamValue,
                                                        'valor da equipa' => $teamValue,
                                                        'pontos base' => $teamPoints[$i]['Base'] / 5);
          }

          $i++;
        }

        return $debugArray;
    }


    /**
     * ratingFix
     * Cria um ponto pivot em torno do rating 5
     * Se um jogador tiver um rating de 5 recebe 100% dos pontos que lhe estavam destinados
     * Se tiver 10 recebe 0%
     * Se tiver 0 recebe 200%
     *
     *
     * @param array $team
     * @return bool
     */

    public function ratingFix($rating, $dif) {
        if($dif >= 0){
            return 2 - (0.2 * $rating);
        }else{
            return 0.2 * $rating;
        }

    }
    /**
     * playerPoints_new
     * faz o rating de cada jogador no jogo seleccionado, usando o novo sistema.
     * O rating final, é o rating no final do jogo.
     *
     * @param array $team
     * @return bool
     */

    public function playerPoints_new($id) {

        $teams = $this->Team->find('all', array('conditions' => array('Team.game_id' => $id), 'recursive' => 1));
        $goalDif = abs($teams[0]['Team']['golos'] - $teams[1]['Team']['golos']);
        $percentDist = $this->percentDist();


        //PREPARAR PONTOS EQUIPAS
        foreach($teams as $team){

            $playerPtsSum = 0;
            foreach($team['Goal'] as $player){

                //rating deste jogador na altura deste jogo
                //procurar o último jogo do jogador que é o segundo item neste array
                $previousGame = $this->Goal->find('all', array('conditions' => array('Goal.game_id <' => $player['game_id'], 'Goal.player_id' => $player['player_id']),
                    'order' => array('Goal.id' => 'desc'),
                    'limit' => 1));

                //se não existirem jogos, usa-se o rating base
                //se existirem usa-se a função playerPointsAvg para calcular o rating de um jogador para um game_id
                if(count($previousGame) == 0){
                    $playerTable = $this->Player->findById($player['player_id']);
                    $currRating[$player['player_id']] = $playerTable['Player']['ratingBase'];
                }
                else{
                    $currRating[$player['player_id']] = $previousGame[0]['Goal']['player_points'];
                }
                $playerPtsSum += $currRating[$player['player_id']];
            }
            //pontos da equipa antes do jogo
            $teamPoints[$team['Team']['id']]['before_game'] = $playerPtsSum;
        }

        //somatório de pontos de ambas as equipas
        $pointsInPot = 0;
        foreach($teamPoints as $value){
            $pointsInPot += $value['before_game'];
        }

        foreach($teams as $team){

            //calcular a nova distribuição pontos das equipas
            if($team['Team']['winner'] == 1){
                $teamPoints[$team['Team']['id']]['after_game'] = (2 * $percentDist[$goalDif]) * $teamPoints[$team['Team']['id']]['before_game'];
            }
            elseif($team['Team']['winner'] == 'empate'){
                $teamPoints[$team['Team']['id']]['after_game'] = $percentDist[0];
            }
            else{
                $teamPoints[$team['Team']['id']]['after_game'] = (2 * (1 - $percentDist[$goalDif])) * $teamPoints[$team['Team']['id']]['before_game'];
            }
        }

        //return $teamPoints;

        //PREPARAR PONTOS JOGADORES
        foreach($teams as $team){

            foreach($team['Goal'] as $player){

                //Calcular os pontos deste jogador
                $playerPoints = ($teamPoints[$team['Team']['id']]['after_game'] * ($currRating[$player['player_id']] / $teamPoints[$team['Team']['id']]['before_game']));

                //aplicar o fix
                $dif = $playerPoints - $currRating[$player['player_id']];
                $ratingFix = $this->ratingFix($currRating[$player['player_id']], $dif);

                $newPts = $dif * $ratingFix;
               // echo $ratingFix."/";
                $playerPoints = $currRating[$player['player_id']] + $newPts;

                $pointsSave = array('Goal' => array('player_points' => $playerPoints,
                            'curr_rating' => $currRating[$player['player_id']],
                            'peso' => round(($currRating[$player['player_id']] / $teamPoints[$team['Team']['id']]['before_game']), 3) * 100,
                            'spPts' => round($newPts, 2),
                            'basePts' => $teamPoints[$team['Team']['id']]['after_game'] - $teamPoints[$team['Team']['id']]['before_game']));

                //debug($playerPoints);
                $this->Goal->id = $player['id'];
                $this->Goal->save($pointsSave);

                //actualizar o rating para a tabela de jogadores, para este jogador
                $this->Player->saveRating($player['player_id'], $playerPoints);

                //return $pointsSave;
            }


        }

    }






/**
 * teste() method
 *
 * função para testes e experiências
 *
 * @param
 * @return
 */

    public function teste() {

       /* for($i = 0; $i <= 14; $i++){
        $var['straight'][$i] = 0.5 + $i*(1/28);
        $var['log'][$i] = log($i, 28);
        }*/
        $x = 14;

        $c = 0.5;
        $b = 0.0618;
        $a = ((1 - $c) - $b*$x) / pow($x, 2);

        for($i = 0; $i <= $x; $i++){
            $y[$i] = $a*pow($i,2) + $b*$i + $c;
        }


        return $y;
    }


/** FUNÇÕES DE STATS *********************************************************************************/

/**
 * gameCount() method
 *
 * devolve o número de jogos feitos até hoje
 *
 * @param
 * @return
 */

    public function gameCount() {
        return $this->find('count');
    }


/**
 * winLose() method
 *
 * devolve uma array com o histórico de vitórias e derrotas por ordem desc para um determinado jogador
 * e a diferença de golos para cada jogo. Derrota com um número negativo.
 *
 * @param
 * @return
 */

    public function winLoseStats($id) {

        $player = $this->Player->find('first', array('conditions' => array('Player.id' => $id)
                                                ,'recursive' => 1));
        foreach($player['Team'] as $team){

            $game = $this->findById($team['game_id']);
            $goal_dif = abs($game['Game']['goal_dif']);

            if($team['winner'] == 1){
                $winLose[$team['game_id']] = $goal_dif;
            }
            else{
                $winLose[$team['game_id']] = -$goal_dif;
            }



        }

        return array_reverse($winLose, true);
    }

/**
 * gameStats() method
 *
 * devolve uma array de jogos ordenados pela maior diferença de golos
 *
 * @param
 * @return
 */

    public function gameStats() {

        $games = $this->find('all');

        /* construcção da array
        estou a usar uma propriedade virtual do modelo: 'goal_dif' definido no topo do model */
        foreach($games as $game){


            //fazer a equipa 'a' ser sempre a vencedora para ficar mais arrumado nos gráficos
            if($game['Game']['team_a'] > $game['Game']['team_b']){
                $team_a = $game['Game']['team_a'];
                $team_b = $game['Game']['team_b'];
            }
            else{
                $team_a = $game['Game']['team_b'];
                $team_b = $game['Game']['team_a'];
            }

            $stats[] = array('id' => $game['Game']['id'],
                             'team_a' => $team_a,
                             'team_b' => $team_b,
                             'goal_sum' => $game['Game']['team_a'] + $game['Game']['team_b'],
                             'goal_dif' => abs($game['Game']['goal_dif']));
        }

        //Sorting da array
        // Obtain a list of columns
        foreach ($stats as $key => $row) {
            $goal_dif[$key]  = $row['goal_dif'];
            $goal_sum[$key] = $row['goal_sum'];
        }

        // Sort the data with volume descending, edition ascending
        // Add $data as the last parameter, to sort by the common key
        array_multisort($goal_dif, SORT_DESC, $goal_sum, SORT_DESC, $stats);

        return $stats;
    }

/** FUNÇÕES UTILITÁRIAS *********************************************************************************/

/**
 * teamIdtoGoal() method
 * adiciona o team id a cada golo
 *
 * @param
 * @return
 */

    public function teamIdtoGoal() {

        $teams = $this->Team->find('all');

        foreach($teams as $team){

            foreach($team['Player'] as $player){
                $goals = array('Goal' => array('team_id' => $team['Team']['id']));

                $search = $this->Goal->find('first', array('conditions' => array('Goal.game_id' => $team['Team']['game_id'],
                    'Goal.player_id' => $player['id'])));
                $this->Goal->id = $search['Goal']['id'];
                $this->Goal->save($goals);
            }

        }
    }

/**
 * resultadoFix() method
 * copia a coluna 'resultado' do jogo para duas colunas, 'team_a' e 'team_b'
 *
 * @param
 * @return
 */

    public function resultadoFix() {

        $games = $this->find('all');

        foreach($games as $game){

            $teams = explode("-", $game['Game']['resultado']);
            //$result[$game['Game']['id']] = $teams;

            $this->id = $game['Game']['id'];
            $this->save(array('Game' => array('team_a' => $teams[0], 'team_b' => $teams[1])));
        }

        return null;
    }
}
