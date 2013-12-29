<?php
App::uses('AppModel', 'Model');
/**
 * Team Model
 *
 * @property Game $Game
 * @property Player $Player
 */
class Team extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'game_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'goals' => array(
			'numeric' => array(
				'rule' => array('numeric'),
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
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Game' => array(
			'className' => 'Game',
			'foreignKey' => 'game_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
        'Goal' => array(
            'className' => 'Goal',
            'foreignKey' => 'team_id',
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
        'Assist' => array(
            'className' => 'Assist',
            'foreignKey' => 'team_id',
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
        'Rating' => array(
            'className' => 'Rating',
            'foreignKey' => 'team_id',
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
			'joinTable' => 'players_teams',
			'foreignKey' => 'team_id',
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
 * generate method
 *
 * @param string $id
 * @return array
 */
    public function generate($id, $invited) {
        //find teams
        $currentTeams = $this->find('all', array('conditions' => array('Team.game_id' => $id)));

        //create teams if they don't exist
        for($i = count($currentTeams); $i < 2; $i++) {
            $this->Create();
            $team = array('Team' => array('game_id' => $id));
            $currentTeams[$i] = $this->save($team);
        }

        //invited players sorting
        foreach ($invited['invited'] as $player) {
            if ($player['available'] == true || is_null($player['available'])) {
                $available[] = $player;
            } else {
                $out[] = $player;
            }
        }

        if(!isset($available)) {
            return null;
        }

        if(!isset($out)) {
            $out = array();
        }

        //Creates empty spots in case players < 10
        while(count($available) < 10) {
            $available[] = array(
                'id' => 0,
                'name' => '__ ? __   ',
                'mean' => null,
                'available' => null);
        }

        //criar uma array com os jogadores extra, o banco
        $bench = array_slice($available, 10, null, true);

        //Cut the array so it has max 10 players
        $lineUp = array_slice($available, 0, 10);

        //quantos jogadores do lineUp principal já disseram que sim
        //vai servir para fazer validação na altura de salvar as equipas
        $lineUpStatus = array();
        foreach ($lineUp as $player) {
            if ($player['available'] == true) {
                $lineUpStatus[] = $player;
            }
        }

        //Sort by rating
        foreach ($lineUp as $key => $row) {
            $player_id[$key]  = $row['id'];
            $mean[$key] = $row['mean'];
        }
        array_multisort($mean, SORT_DESC, $player_id, SORT_ASC, $lineUp);


        $ratingTotal = 0;
        //Find the overall rating of the 10 players
        foreach($lineUp as $player) {
            $ratingTotal += $player['mean'];
        }
        //ideal ranking for each team
        $idealTeamRating = $ratingTotal / 2;


        $len = count($lineUp);
        $bestComb = $ratingTotal;
        // $teams[0]['Team'] = '';
        //do all combinations of players and save the best one
        for ($i = 1; $i < $len - 2; $i++) {
            for ($j = $i + 1; $j < $len - 1; $j++) {
                for ($k = $j + 1; $k < $len; $k++) {
                    for ($m = $k + 1; $m < $len; $m++) {
                        for ($n = $m + 1; $n < $len; $n++) {
                            //Team Rating
                            $teamRating = $lineUp[$i]['mean'] + 
                                $lineUp[$j]['mean'] + 
                                $lineUp[$k]['mean'] + 
                                $lineUp[$m]['mean'] + 
                                $lineUp[$n]['mean'];
                            //If the difference between this Team rating and the ideal rating is smaller, 
                            //save as best combination
                            if(abs($idealTeamRating - $teamRating) < $bestComb) {
                                $bestComb = abs($idealTeamRating - $teamRating);
                                
                                $teams[0]['Player'] = array(
                                    $i => $lineUp[$i],
                                    $j => $lineUp[$j],
                                    $k => $lineUp[$k],
                                    $m => $lineUp[$m],
                                    $n => $lineUp[$n],
                                    );

                                $teams[0]['Team']['rating'] = $teamRating;
                            }
                        }
                    }
                }
            }
        }


        //remove players from the first team from the available list to end up with team 2
        for ($i = 1; $i <= 10; $i++) {
            foreach($teams[0]['Player'] as $key => $player){
                if(isset($lineUp[$i]) and ($i == $key)){
                    unset($lineUp[$i]);
                }
            }
        }
        //setup variables for the other team
        $teams[1]['Player'] = $lineUp;
        $teams[1]['Team']['rating'] = $ratingTotal - $teams[0]['Team']['rating'];

        //add team id
        $teams[0]['Team']['id'] = $currentTeams[0]['Team']['id'];
        $teams[1]['Team']['id'] = $currentTeams[1]['Team']['id'];

        //devolve uma array com 3 arrays interiores
        return $list = array('teams' => $teams,
                             'bench' => $bench,
                             'out' => $out,
                             'lineUpStatus' => count($lineUpStatus)
                             );
    }

    public function players($id = null){

        $options = array('conditions' => array('team_id' => $id));
        return $this->PlayersTeam->find('all', $options);
    }

/**
 * isWinner method
 * 
 * @param  int  $teamID
 * @return boolean
 */
    public function isWinner($teamID)
    {
        return $this->find('count', array(
            'conditions' => array('Team.id' => $teamID, 'Team.is_winner' => 1) ));
    }

/**
 * tristate method
 * 
 * Gera uma série com os últimos resultados de um jogador (vitórias, derrotas),
 * para ser usado pelo sparklines na sidebar
 * @param  int $playerId
 * @param  int $limit    número de resultados pretendidos
 * @return string
 */
    public function tristate($playerId, $limit = null)
    {
        $tristate = null;

        $teams = $this->PlayersTeam->find('all', array(
            'conditions' => array('PlayersTeam.player_id' => $playerId),
            'order' => array('PlayersTeam.id' => 'desc'),
            'limit' => $limit));

        //inverter porque queremos os resultados mais recentes à direita
        $teams = array_reverse($teams);

        //trocar o '0' por '-1' para o sparklines
        foreach ($teams as $team) {
            if (!$this->isWinner($team['PlayersTeam']['team_id'])) {
                $tristate .= '-1,';
            } else {
                $tristate .= '1,';
            }
        }

        //tirar a última virgula (porque não é necessária) e devolver
        return rtrim($tristate, ",");
    }





}
