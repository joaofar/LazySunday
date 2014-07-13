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
 * @return array
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

        //no caso do jogador não ter vitórias
        if (!isset($wins)) {
            return array(
                'wins' => 0,
                'win_percentage' => 0,
                'wins_limit' => 0,
                'win_percentage_limit' => 0);
        }

        return array(
            'wins' => $wins,
            'win_percentage' => round($wins / count($teams['Team']), 2),
            'wins_limit' => $wins_limit,
            'win_percentage_limit' => round($wins_limit / $limit, 2));
    }

/**
 * countGoals method
 *
 * @param int $id, int $limit
 * @return array [goals, goals_limit]
 */
    public function countGoals($id = null, $limit = null) {
        //array com todos os golos do jogador
        $options = array('conditions' => array('player_id' => $id),
                         'order' => array('Goal.id DESC'));
        $allGoals = $this->Goal->find('all', $options);

        //número de jogos onde foram registados golos
        $countGames = count($allGoals);

        //quando o limite é inferior ao número de jogos
        if ($countGames < $limit) {
            $limit = $countGames;
        }

        //no caso do jogador não ter golos
        if (count($allGoals) == 0) {
            return array(
                'goals' => 0, 
                'goals_limit' => 0,
                'goals_average' => 0,
                'goals_average_limit' => 0);
        }

        //contagem...
        foreach($allGoals as $key => $goal) {
            if (!isset($count['goals'])) {
                $count['goals'] = $goal['Goal']['goals'];
            } else {
                $count['goals'] += $goal['Goal']['goals'];
            }

            //guardar contagem no limite  definido
            if ($key == ($limit - 1)) {
                $count['goals_limit'] = $count['goals'];
            }
        }

        return array_merge($count, array(
            'goals_average' => round($count['goals'] / $countGames, 2),
            'goals_average_limit' => round($count['goals_limit'] / $limit, 2)));
    }

/**
 * countAssists method
 *
 * @param int $id, int $limit
 * @return array [assists, assists_limit]
 */
    public function countAssists($id = null, $limit = null) {
        //array com todos os golos do jogador
        $options = array('conditions' => array('player_id' => $id),
                         'order' => array('Assist.id DESC'));
        $allAssists = $this->Assist->find('all', $options);

        //número de jogos onde foram registados golos
        $countGames = count($allAssists);

        //quando o limite é inferior ao número de jogos
        if ($countGames < $limit) {
            $limit = $countGames;
        }

        //no caso do jogador não ter golos
        if (count($allAssists) == 0) {
            return array(
                'goals' => 0, 
                'goals_limit' => 0,
                'goals_average' => 0,
                'goals_average_limit' => 0);
        }

        //contagem...
        foreach($allAssists as $key => $assist) {
            if (!isset($count['assists'])) {
                $count['assists'] = $assist['Assist']['assists'];
            } else {
                $count['assists'] += $assist['Assist']['assists'];
            }

            //guardar contagem no limite  definido
            if ($key == ($limit - 1)) {
                $count['assists_limit'] = $count['assists'];
            }
        }

        return array_merge($count, array(
            'assists_average' => round($count['assists'] / $countGames, 2),
            'assists_average_limit' => round($count['assists_limit'] / $limit, 2)));
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
        $player = $this->find('first', array('conditions' => array('Player.id' => $id), 'contain' => array('Team')));
        $presencas = $this->countGamesPlayed($id);
        if($presencas <= $limit){
            $presencas_limit = $presencas;
        }else{
            $presencas_limit = $limit;
        }

        $games = $this->Game->find('all', array('order' => array('Game.id DESC'), 'contain' => array('Team')));
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
                   } else {
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
            echo $player['Player']['id'];
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
        $player['games_played'] = $this->countGamesPlayed($id);
        //no caso do jogador não ter jogos não vale a pena prosseguir com o cálculo
        if ($player['games_played'] == 0) {
            return null;
        }

        //no caso do jogador ter um número de jogos inferior ao limite, o número de jogos
        //passa a ser o limite
        if ($player['games_played'] < $limit) {
            $limit = $player['games_played'];
        }

        //WINS
        //a função countWins() cria logo uma array com os campos certos, 
        //portanto basta juntar o resultado com o array_merge() à array Player
        $player = array_merge($player, $this->countWins($id, $limit));

        //GOALS
        $player = array_merge($player, $this->countGoals($id, $limit));

        //ASSISTS
        $player = array_merge($player, $this->countAssists($id, $limit));
        

        //EQUIPA M/S
        $teamSC = $this->equipaMS($id, $limit);
        //EQUIPA M/S (DESDE SEMPRE)
        $player['team_scored'] = $teamSC['M'];
        $player['team_scored_average'] = $teamSC['M_p_jogo'];
        $player['team_conceded'] = $teamSC['S'];
        $player['team_conceded_average'] = $teamSC['S_p_jogo'];
        //EQUIPA M/S (LIMIT)
        $player['team_scored_limit'] = $teamSC['M_limit'];
        $player['team_scored_average_limit'] = $teamSC['M_p_jogo_limit'];
        $player['team_conceded_limit'] = $teamSC['S_limit'];
        $player['team_conceded_average_limit'] = $teamSC['S_p_jogo_limit'];

        //SAVE PLAYER DATA
        $this->id = $id;
        if ($this->exists()) {
            $this->save(array('Player' => $player));
            return $player;
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

        if (isset($goalsAssists)) {
            return $goalsAssists;
        } else {
            return null;
        }
        

    }

}
