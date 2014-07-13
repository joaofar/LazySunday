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
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Assist' => array(
			'className' => 'Assist',
			'foreignKey' => 'game_id',
			'dependent' => true,
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
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '0',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'game_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	'Rating' => array(
	  'className' => 'Rating',
	  'foreignKey' => 'game_id',
	  'dependent' => true,
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

	// public $virtualFields = array(
	// 	'goal_dif' => 'Game.team_a_score - Game.team_b_score'
	// );



/**
 * details  method
 * 
 * @param  int $id
 * @return array     varivel para ser usada na view
 */
	public function details($id)
	{ 
		//TEAMS
		$teams = $this->Team->find('all', array(
			'conditions' => array('Team.game_id' => $id),
		    'recursive' => 1));
		
		$i = 0;
		foreach ($teams as $team) {
		//TEAM SCORE
		$details[$i]['Team'] = array('score' => $team['Team']['score']);

			//PLAYERS PERFORMANCE DETAILS
			foreach ($team['Rating'] as $rating) {
				$previousRating = $this->Rating->getPrevious($rating['id'], $rating['player_id']);

				$details[$i]['Player'][$rating['player_id']] = array(
					'currentRating' => $rating['mean'],
					'previousRating' => $previousRating['mean'],
					'difference' => $rating['mean'] - $previousRating['mean'],
					'standardDeviation' => $rating['standard_deviation']
					);
			}
			// name
			foreach ($team['Player'] as $player) {
				$details[$i]['Player'][$player['id']]['name'] = $player['name'];
			}
			// goals
			foreach ($team['Goal'] as $goal) {
			    $details[$i]['Player'][$goal['player_id']]['goals'] = $goal['goals'];
		  	}
		  	// assists
			if (isset($team['Assist'][0])) {
				foreach ($team['Assist'] as $assist) {
			    $details[$i]['Player'][$assist['player_id']]['assists'] = $assist['assists'];
		  		}
			} else {
				foreach ($team['Player'] as $player) {
				$details[$i]['Player'][$player['id']]['assists'] = null;
				}
			}

		  	$i++;
		}
		//GAME DETAILS
	  	return $details;
	}

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

			$goals = $this->Goal->find('all', array(
			  'conditions' => array('team_id' => $team['id']), 
			  'recursive' => 1));


			${'team_'.$i.'_score'} = 0;
			foreach($goals as $goal){

			  ${'team_'.$i.'_data'}[$goal['Player']['name']] = array(
				  'id' => $goal['Goal']['player_id'],
				  'goals' => $goal['Goal']['goals'],
				  'assists' => $goal['Goal']['assists'],
				  'player_points' => $goal['Goal']['player_points'],
				  'curr_rating' => $goal['Goal']['curr_rating'],
				  'peso' => $goal['Goal']['peso'],
				  'basePts' => $goal['Goal']['basePts'],
				  'spPts' => $goal['Goal']['spPts']
				  );

				${'team_'.$i.'_score'} += $goal['Goal']['goals'];
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
 * submitScore method
 * 
 * @param  array $data [$this->request->data]
 * @return boolean
 */
	public function submitScore($data)
	{
		// save Goals/Assists
        if (isset($data['Goal'])) {
        	// save assists
        	if (isset($data['Assist'])) {
        		if (!$this->Assist->saveMany($data['Assist'])) {
        			return false;
        		}
        	}
            // save all goals and assists
        if (!$this->Goal->saveMany($data['Goal'])) {
                return false;
            }
            
            //prepare team data
            foreach ($data['Goal'] as $player) {
                $teamGoals[$player['team_id']][] = $player['goals'];
            }

            foreach ($teamGoals as $teamId => $goals) {
                $data['Team'][] = array(
                    'id' => $teamId, 
                    'score' => array_sum($goals)
                    );
            }
        }

        // update Teams score/is_winner
        if (isset($data['Team'])) {
            
            // descobrir qual a equipa vencedora
            if ($data['Team'][0]['score'] > $data['Team'][1]['score']) {
                $data['Team'][0]['is_winner'] = 1;
                $data['Team'][1]['is_winner'] = 0;
            } else {
                $data['Team'][0]['is_winner'] = 0;
                $data['Team'][1]['is_winner'] = 1;
            }
                
            if (!$this->Team->saveMany($data['Team'])){
            	return false;
            }
        }

        return true;
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
 * goalDifference method
 * 
 * @param  int $id game_id
 * @return int
 */
	public function goalDifference($id)
	{
		$game = $this->find('first', array(
				'conditions' => array('id' => $id),
				'contain' => array('Team.score')
				));

		return abs($game['Team'][0]['score'] - $game['Team'][1]['score']);
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

		$player = $this->Player->find('first', array(
			'conditions' => array('Player.id' => $id),
			'contain' => array('Team')
			));

		foreach($player['Team'] as $team){

			$goalDifference = $this->goalDifference($team['game_id']);

			if($team['is_winner'] == 1){
				$winLose[$team['game_id']] = $goalDifference;
			}
			else{
				$winLose[$team['game_id']] = - $goalDifference;
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
}
