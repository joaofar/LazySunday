<?php
App::uses('AppModel', 'Model');
/**
 * Player Model
 *
 * @property Goal $Goal
 * @property Invite $Invite
 * @property Team $Team
 */
class Player extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'games_played' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'wins' => array(
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
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Goal' => array(
			'className' => 'Goal',
			'foreignKey' => 'player_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => 'id DESC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
        'Assist' => array(
            'className' => 'Assist',
            'foreignKey' => 'player_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => 'id DESC',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
		'Invite' => array(
			'className' => 'Invite',
			'foreignKey' => 'player_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => 'id DESC',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
        'Rating' => array(
            'className' => 'Rating',
            'foreignKey' => 'player_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => 'id DESC',
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
		'Team' => array(
			'className' => 'Team',
			'joinTable' => 'players_teams',
			'foreignKey' => 'player_id',
			'associationForeignKey' => 'team_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
        'Game' => array(
            'className' => 'Game',
            'joinTable' => 'games_players',
            'foreignKey' => 'player_id',
            'associationForeignKey' => 'game_id',
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
 * allPlayers method
 * Devolve uma array com a informação da tabela de jogadores
 *
 * @param
 * @return
 */

    public function currentRating($id) {

        //rating deste jogador na altura deste jogo
        //procurar o último jogo do jogador que é o segundo item neste array
        $previousGame = $this->Goal->find('all', array(
            'conditions' => array(
                'Goal.game_id <' => $player['game_id'], 
                'Goal.player_id' => $player['player_id']),
            'order' => array('Goal.id' => 'desc'),
            'limit' => 1));

        //se não existirem jogos, usa-se o rating base
        //se existirem usa-se a função playerPointsAvg para calcular o rating de um jogador para um game_id
        if(count($previousGame) == 0){
            $playerTable = $this->Player->findById($player['player_id']);
            $player['curr_rating'] = $playerTable['Player']['rating_base_elo'];
        }
        else{
            $player['curr_rating'] = $previousGame[0]['Goal']['player_points'];
        }

    }

/**
 * allPlayers method
 * Devolve uma array com a informação da tabela de jogadores
 *
 * @param
 * @return
 */

    public function allPlayers() {

        $limit = Configure::read('limit');

        $options = array('order' => array('Player.ratingLouie' => 'desc'),
            'conditions' => array('Player.games_played >=' => $limit));
        return $this->find('all', $options);

    }

/**
 * countGamesPlayed method
 *
 * @param string $id
 * @return int
 */
    public function countGamesPlayed($id = null, $gameID = null) {
        //find all gamesPlayed
        if(!isset($gameID)){
            $options = array('conditions' => array('player_id' => $id));

            return $this->PlayersTeam->find('count', $options);
        } else {
            //find gamesPlayed until designated gameID
            $player = $this->findById($id);
            
            $gamesPlayed = 0;
            foreach($player['Team'] as $team){
                if($team['game_id'] <= $gameID){
                $gamesPlayed += 1;
                }
            }
            return $gamesPlayed; 
        }
    }

/**
 * wins method
 *
 * @param string $id
 * @return int
 */
    public function countWins($id = null, $limit = null) {
        $options = array('conditions' => array('player_id' => $id), 'limit' => $limit);
        $gamesPlayed = $this->PlayersTeam->find('all', $options);

        $wins = 0;
        foreach($gamesPlayed as $team){
        $options = array('conditions' => array('Team.id' => $team['PlayersTeam']['team_id'], 'is_winner' => 1));
            if($this->Team->find('first', $options)) {
                $wins += 1;
            }
        }
        return $wins;
    }

/**
 * goals method
 *
 * @param string $id
 * @return int
 */
    public function countGoals($id = null, $limit = null) {
        $options = array('conditions' => array('player_id' => $id),
                         'order' => array('Goal.id DESC'),
                         'limit' => $limit);
        $goals = $this->Goal->find('all', $options);

        $total = 0;
        foreach($goals as $goal) {
            $total += $goal['Goal']['goals'];
        }

        return $total;
    }

/**
 * bestGoalAverage method
 *
 * @param string $id
 * @return float
 */
    public function bestGoalAverage() {
        $options = array('order' => array('Player.goal_average' => 'desc', 'Player.games_played' => 'desc'),
            'conditions' => array('Player.games_played >=' => self::N_MIN_PRE));
        return $this->find('first', $options);
    }

/**
 * equipaMS method
 *
 * @param string $id
 * @return array
 */
    public function equipaMS($id = null) {

        //data
        $player = $this->find('first', array('conditions' => array('Player.id' => $id), 'recursive' => 1));
        $presencas = $this->countPresencas($id);
        if($presencas <= $limit){
            $presencas_limit = $presencas;
        }else{
            $presencas_limit = $limit;
        }

        $games = $this->Game->find('all', array('order' => array('Game.id DESC'), 'recursive' => 1));
        $ignoreTeams = array(5, 6, 7, 8, 9, 10);

        //init var
        $equipaM = array();
        $equipaS = array();

        //create arrays of the games played by this player and the teams he belonged to
        foreach($player['Team'] as $team){
            if(!in_array($team['id'], $ignoreTeams)) {
            $gamesPlayed[] = $team['game_id'];
            $teamsPlayed[] = $team['id'];
            }
        }

        //in case player is not on any teams return 0
        if(!isset($teamsPlayed)){
            return array('M' => 0, 'S' => 0, 'M_p_jogo' => 0, 'S_p_jogo' => 0);
        }

        //cycle game by game
        foreach($games as $game){
            //cycle both teams of that game
            foreach($game['Team'] as $team){
                //If the player played in this game...
                if((!in_array($team['id'], $ignoreTeams)) and (in_array($team['game_id'], $gamesPlayed) )) {

                    //... and played in this team
                   if(in_array($team['id'], $teamsPlayed)){
                       $equipaM[$team['game_id']] = $team['score'];
                   }
                   else{
                       $equipaS[$team['game_id']] = $team['score'];
                   }
                }

            }
        }


        //Calcula os valores desde sempre
        $equipaMS['M'] = 0;
        $equipaMS['S'] = 0;
        foreach($equipaM as $game){
            $equipaMS['M'] += $game;
        }

        foreach($equipaS as $game){
            $equipaMS['S'] += $game;
        }

        //Calcula os valores com limite
        $equipaM = array_slice($equipaM, 0, $limit);
        $equipaS = array_slice($equipaS, 0, $limit);
        $equipaMS['M_limit'] = 0;
        $equipaMS['S_limit'] = 0;
        foreach($equipaM as $game){
            $equipaMS['M_limit'] += $game;
        }

        foreach($equipaS as $game){
            $equipaMS['S_limit'] += $game;
        }


        //Calcula p_jogo
        $equipaMS['M_p_jogo'] = round($equipaMS['M']/$presencas, 2);
        $equipaMS['S_p_jogo'] = round($equipaMS['S']/$presencas, 2);
        //Calcula p_jogo
        $equipaMS['M_p_jogo_limit'] = round($equipaMS['M_limit']/$presencas_limit, 2);
        $equipaMS['S_p_jogo_limit'] = round($equipaMS['S_limit']/$presencas_limit, 2);

        return $equipaMS;
    }

/**
 * updateStats_allPlayers
 * actualiza as stats de todos os jogadores
 *
 * @param string $id
 * @return void
 */

    public function updateStats_allPlayers() {

        $players = $this->find('all');
        foreach($players as $player) {
            $this->updateStats($player['Player']['id']);
        }
    }

/**
 * updateStats method
 * actualiza as stats de um jogador
 *
 * @param int $id
 * @return void
 */
    public function updateStats($id) {
        //GAMES PLAYED
        $Player['games_played'] = $this->countGamesPlayed($id);

        //WINS
        $Player['wins'] = $this->countWins($id, null);

        //WIN PERCENTAGE
        if($Player['wins'] == 0){
            $Player['win_percentage'] = 0;
        }else{
            $Player['win_percentage'] = round($Player['wins'] / $Player['games_played'], 3);
        }

        //GOALS
        $Player['goals'] = $this->countGoals($id, null);

        //GOALS AVERAGE
        if($Player['goals'] != 0) {
            $Player['goals_average'] = round($Player['goals'] / $Player['games_played'], 2);
        }else{
            $Player['goals_average'] = 0;
        }

        //ASSISTS
        $assists = $this->assists($id, $limit);
        
        $Player['assists'] = $assists['assists'];
        $Player['assists_average'] = $assists['assists_average'];

        //EQUIPA M/S
        $teamSC = $this->equipaMS($id, $limit);
        //EQUIPA M/S (DESDE SEMPRE)
        $Player['team_scored'] = $teamSC['M'];
        $Player['team_scored_average'] = $teamSC['M_average'];
        $Player['equipa_s'] = $teamSC['S'];
        $Player['equipa_s_average'] = $teamSC['S_average'];

        //SAVE PLAYER DATA
        $this->id = $id;
        if ($this->exists()) {
            $this->save(array('Player' => $Player));
            return $Player;
        }else{
            throw new NotFoundException(__('jogador inválido'));
        }

    }



/**
 * verifica se um jogador percente a uma equipa
 *
 * @param array $team, array $player
 * @return bool
 */
    private function belongsToTeam($team, $player) {
        foreach($team['Player'] as $teamPlayer) {
            if($teamPlayer['id'] == $player['Player']['id']) {
                return true;
            }
        }
        return false;
    }

/**
 * verifica se uma equipa foi vencedora
 *
 * @param array $team
 * @return bool
 */
    private function isTeamWinner($team) {
        if($team['Team']['is_winner']) { return true; } else { return false; }
    }

/**
 * calcula as assistências de um determinado jogador
 *
 * @param none
 * @return none
 */
    public function assists($id, $limit = null) {
        //jogo a partir do qual se começou a contar as assistências
        $gameId = 59;

        //encontrar as assistências que são guardadas na tabela dos golos
        $games = $this->Goal->find('all', array(
            'conditions' => array('game_id >=' => $gameId, 'player_id =' => $id),
            'order' => array('Goal.id' => 'desc')));


        //nº de jogos com assistências
        $nGames = count($games);
        if($nGames == 0){
            return array(
                'all' => array('assists' => 0, 'assists_average' => 0),
                'limit' => array('assists' => 0, 'assists_average' => 0)
                );
        }

        if($nGames < $limit){
            $nGames_limit = $nGames;
        }else{
            $nGames_limit = $limit;
        }



        //somar assistências totais
        $assists['assist'] = 0;
        foreach($games as $game){
            //criar lista para poder cortar e usar mais tarde noas stats com limite
            $assistsList[] = $game['Goal']['assists'];
            $assists['assist'] += $game['Goal']['assists'];
        }

        //somar assistências dentro do limite definido
        $assistsList = array_slice($assistsList, 0, $limit);
        $assists['assist_limit'] = 0;
        foreach($assistsList as $assist){
            $assists['assist_limit'] += $assist;
        }


        //assistências por jogo desde sempre
        if($assists['assist'] != 0){
        $assists['assist_p_jogo'] = round($assists['assist'] / $nGames, 2);
        }else{
        $assists['assist_p_jogo'] = 0;
        }

        //assistências por jogo dentro do limite
        if($assists['assist_limit'] != 0){
            $assists['assist_p_jogo_limit'] = round($assists['assist_limit'] / $nGames_limit, 2);
        }else{
            $assists['assist_p_jogo_limit'] = 0;
        }

        return $assists;
    }



/**
 * STATS
 * goals() method
 *
 * @param
 * @return array
 */

    public function goalsAssists($id, $limit) {

        //jogos em que este jogador participou com ou sem discriminação de golos
        $player = $this->find('all', array(
            'conditions' => array('id =' => $id),
            'contain' => array(
                'Game' => array(
                    'order' => array('Game.id' => 'desc'),
                    'limit' => 20))
            ));

        foreach ($player[0]['Game'] as $game) {
             $goals = $this->Goal->find('first', array(
                'conditions' => array(
                    'game_id' => $game['id'],
                    'player_id' => $id
                    )));

             $assists = $this->Assist->find('first', array(
                'conditions' => array(
                    'game_id' => $game['id'],
                    'player_id' => $id
                    )));

            if (!isset($goals['Goal']['goals'])) {
                $goals['Goal']['goals'] = '-0.5';
            }
            
            if (!isset($assists['Assist']['assists'])) {
                $assists['Assist']['assists'] = '-0.5';
            }

             $goalsAssists[$game['id']] = array(
                'Goals' => $goals['Goal']['goals'],
                'Assists' => $assists['Assist']['assists']
                );
            
        }

        return $goalsAssists;

    }

}
