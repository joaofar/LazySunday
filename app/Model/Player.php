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
 * countWins method
 *
 * @param string $id
 * @return int
 */
    public function countWins($id = null, $limit = null) {
        
        //procura-se o jogador e associa-se o modelo das equipas onde jogou
        $teams = $this->find('first', array(
            'conditions' => array('Player.id' => $id), 
            'contain' => array(
                'Team' => array(
                    'order' => array('Team.id DESC')))));

        //conta-se o número de vitórias
        foreach ($teams['Team'] as $key => $team) {

            if ($team['is_winner'] == 1) {
                if (!isset($wins)) {
                    $wins = 1;
                } else {
                    $wins += 1;
                }
            }

            //guardar as vitórias num limite definido
            if (isset($wins)) {
                if ($key < $limit) {
                $wins_limit = $wins;
                }
            } else {
                $wins_limit = 0;
            }
            
        }

        return array(
            'wins' => $wins,
            'win_percentage' => round($wins / count($teams['Team']), 2),
            'wins_limit' => $wins_limit,
            'win_percentage_limit' => round($wins_limit / $limit, 2));
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
    public function equipaMS($id = null, $limit = null) {

        //data
        $player = $this->find('first', array('conditions' => array('Player.id' => $id), 'recursive' => 1));
        $presencas = $this->countGamesPlayed($id);
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
            $this->updateStats($player['Player']['id'], 20);
        }
    }

/**
 * updateStats method
 * actualiza as stats de um jogador
 *
 * @param int $id
 * @return void
 */
    public function updateStats($id, $limit) {
        //GAMES PLAYED
        $Player['games_played'] = $this->countGamesPlayed($id);

        //no caso do jogador ter um número de jogos inferior ao limite
        if ($Player['games_played'] < $limit) {
            $limit = $Player['games_played'];
        }

        //WINS
        $wins = $this->countWins($id, $limit);
        $Player['wins'] = $wins['wins'];
        $Player['win_percentage'] = $wins['win_percentage'];
        $Player['wins_limit'] = $wins['wins_limit'];
        $Player['win_percentage_limit'] = $wins['win_percentage_limit'];


        //GOALS
        $Player['goals'] = $this->countGoals($id, null);
        $Player['goals_limit'] = $this->countGoals($id, $limit);

        //GOALS AVERAGE
        if($Player['goals'] != 0) {
            $Player['goals_average'] = round($Player['goals'] / $Player['games_played'], 2);
            $Player['goals_average_limit'] = round($Player['goals_limit'] / $limit, 2);          
        } else {
            $Player['goals_average'] = 0;
        }

        //ASSISTS
        $assists = $this->assists($id, $limit);
        
        $Player['assists'] = $assists['assists'];
        $Player['assists_limit'] = $assists['assist_limit'];
        $Player['assists_average'] = $assists['assist_p_jogo'];
        $Player['assists_average_limit'] = $assists['assist_p_jogo_limit'];

        //EQUIPA M/S
        $teamSC = $this->equipaMS($id, $limit);
        //EQUIPA M/S (DESDE SEMPRE)
        $Player['team_scored'] = $teamSC['M'];
        $Player['team_scored_average'] = $teamSC['M_p_jogo'];
        $Player['team_conceded'] = $teamSC['S'];
        $Player['team_conceded_average'] = $teamSC['S_p_jogo'];
        //EQUIPA M/S (LIMIT)
        $Player['team_scored_limit'] = $teamSC['M_limit'];
        $Player['team_scored_average_limit'] = $teamSC['M_p_jogo_limit'];
        $Player['team_conceded_limit'] = $teamSC['S_limit'];
        $Player['team_conceded_average_limit'] = $teamSC['S_p_jogo_limit'];

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
        $games = $this->Assist->find('all', array(
            'conditions' => array('game_id >=' => $gameId, 'player_id =' => $id),
            'order' => array('Assist.id' => 'desc')));


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
        $assists['assists'] = 0;
        foreach($games as $game){
            //criar lista para poder cortar e usar mais tarde nas stats com limite
            $assistsList[] = $game['Assist']['assists'];
            $assists['assists'] += $game['Assist']['assists'];
        }

        //somar assistências dentro do limite definido
        $assistsList = array_slice($assistsList, 0, $limit);
        $assists['assist_limit'] = 0;
        foreach($assistsList as $assist){
            $assists['assist_limit'] += $assist;
        }


        //assistências por jogo desde sempre
        if($assists['assists'] != 0){
        $assists['assist_p_jogo'] = round($assists['assists'] / $nGames, 2);
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
