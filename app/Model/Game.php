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

        $goaloptions = array('conditions' => array('game_id' => $id));
        $goals = $this->Goal->find('all', $goaloptions);



        $i = 1;
        $team_1_score = 0;
        $team_2_score = 0;
        foreach($goals as $data){
            if($i <= 5){
                $team_1_data[$data['Player']['nome']]['golos'] = $data['Goal']['golos'];
                $team_1_data[$data['Player']['nome']]['assistencias'] = $data['Goal']['assistencias'];
                $team_1_data[$data['Player']['nome']]['player_points'] = $data['Goal']['player_points'];

                $team_1_score =+ $team_1_score + $data['Goal']['golos'];
            }
            else{
                $team_2_data[$data['Player']['nome']]['golos'] = $data['Goal']['golos'];
                $team_2_data[$data['Player']['nome']]['assistencias'] = $data['Goal']['assistencias'];
                $team_2_data[$data['Player']['nome']]['player_points'] = $data['Goal']['player_points'];

                $team_2_score =+ $team_2_score + $data['Goal']['golos'];
            }

            $i++;
        }

        //debug($team_1_data);

        return array('team_1_data' => $team_1_data,
                     'team_2_data' => $team_2_data,
                     'team_1_score' => $team_1_score,
                     'team_2_score' => $team_2_score);


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

        //número de jogos que são necessários ganhar para se ter 100% dos pontos
        $x = 14;

        //valor base qd a diferença de golos é 0, quer dizer que cada equipa recebe 0.5 (50% dos pontos)
        $c = 0.5;

        //este valor determina a inclinação da curva
        $b = 0.0618;
        $a = ((1 - $c) - $b*$x) / pow($x, 2);

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

    public function allPlayerPoints() {

        $games = $this->find('all');

        foreach($games as $game){

            $game['Game']['id'];
            $this->playerPoints($game['Game']['id']);
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

        $teams = $this->Team->find('all', array('conditions' => array('Team.game_id' => $id)));

        //$totalGoals = $teams[0]['Team']['golos'] + $teams[1]['Team']['golos'];
        echo $goalDif = abs($teams[0]['Team']['golos'] - $teams[1]['Team']['golos']);

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
            $teamPoints[$i]['Base'] = ($teamPoints[$i]['Team'] * (1 - $pointsWeight))/5;

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

          foreach($team['Goal'] as $player){

              $goalPoints = $player['golos'] * $pointsPerGoal;
              $assistPoints = $player['assistencias'] * $pointsPerAssist;

              //somar os pontos base mais os pontos especiais
              $playerPoints = $teamPoints[$i]['Base'] + ($goalPoints + $assistPoints);

              //$pointsSave = array('Goal' => array('game_id' => $id, 'player_id' => $player['player_id'], 'player_points' => $playerPoints));
              $pointsSave = array('Goal' => array('player_points' => $playerPoints));
              $this->Goal->id = $player['id'];
              $this->Goal->save($pointsSave);
          }

          $i++;
        }
    }




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

        return $goal;
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


/** FUNÇÕES DE STATS */

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


}
