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
		'presencas' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'vitorias' => array(
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
            'conditions' => array('Player.presencas >=' => $limit));
        return $this->find('all', $options);

    }

/**
 * countPresencas method
 *
 * @param string $id
 * @return int
 */
    public function countPresencas($id = null, $gameID = null) {
        //find all presencas
        if(!isset($gameID)){
            $options = array('conditions' => array('player_id' => $id));
            return $this->PlayersTeam->find('count', $options);
        }
        //find presencas until designated gameID
        else{

            //data
            $player = $this->findById($id);
            //debug($player);

            //var
            $presencas = 0;

            //
            foreach($player['Team'] as $team){
                if($team['game_id'] <= $gameID){
                $presencas += 1;
                }
            }

            return $presencas;
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
        $presencas = $this->PlayersTeam->find('all', $options);

        $wins = 0;
        foreach($presencas as $team){
        $options = array('conditions' => array('Team.id' => $team['PlayersTeam']['team_id'], 'winner' => 1));
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
        $options = array('order' => array('Player.golos_p_jogo' => 'desc', 'Player.presencas' => 'desc'),
            'conditions' => array('Player.presencas >=' => self::N_MIN_PRE));
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
                       $equipaM[$team['game_id']] = $team['goals'];
                   }
                   else{
                       $equipaS[$team['game_id']] = $team['goals'];
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
 * @param string $id
 * @return void
 */
    public function updateStats($id) {

        //variavel global guardada em Config/bootstrap.php
        $limit = Configure::read('limit');

        //PRESENÇAS
        $Player['presencas'] = $this->countPresencas($id);

        if($Player['presencas'] < $limit){
            $Player['presencas_limit'] = $Player['presencas'];
        }else{
            $Player['presencas_limit'] = $limit;
        }

        //VICTÓRIAS
        $Player['vitorias'] = $this->countWins($id, null);
        $Player['vitorias_limit'] = $this->countWins($id, $limit);

        //VITÓRIAS / PRESENÇAS
        if($Player['vitorias'] == 0){
            $Player['vit_pre'] = 0;
        }else{
            $Player['vit_pre'] = round($Player['vitorias'] / $Player['presencas'], 3);
            $Player['vit_pre_limit'] = round($Player['vitorias_limit'] / $Player['presencas_limit'], 3);
        }

        //goals
        $Player['goals'] = $this->countGoals($id, null);
        $Player['golos_limit'] = $this->countGoals($id, $limit);

        //GOLOS P/ JOGO (DESDE SEMPRE)
        if($Player['goals'] != 0) {
            $Player['golos_p_jogo'] = round($Player['goals'] / $Player['presencas'], 2);
        }else{
            $Player['golos_p_jogo'] = 0;
        }
        //GOLOS P/ JOGO (LIMIT)
        if($Player['goals'] != 0) {
            $Player['golos_p_jogo_limit'] = round($Player['golos_limit'] /
                $Player['presencas_limit'], 2);
        }else{
            $Player['golos_p_jogo_limit'] = 0;
        }

        //ASSISTÊNCIAS
        $assists = $this->assists($id, $limit);
        //ASSISTÊNCIAS (DESDE SEMPRE)
        $Player['assist'] = $assists['assist'];
        $Player['assist_p_jogo'] = $assists['assist_p_jogo'];
        //ASSISTÊNCIAS (LIMIT)
        $Player['assist_limit'] = $assists['assist_limit'];
        $Player['assist_p_jogo_limit'] = $assists['assist_p_jogo_limit'];

        //EQUIPA M/S
        $equipaMS = $this->equipaMS($id, $limit);
        //EQUIPA M/S (DESDE SEMPRE)
        $Player['equipa_m'] = $equipaMS['M'];
        $Player['equipa_m_p_jogo'] = $equipaMS['M_p_jogo'];
        $Player['equipa_s'] = $equipaMS['S'];
        $Player['equipa_s_p_jogo'] = $equipaMS['S_p_jogo'];
        //EQUIPA M/S (LIMIT)
        $Player['equipa_m_limit'] = $equipaMS['M_limit'];
        $Player['equipa_m_p_jogo_limit'] = $equipaMS['M_p_jogo_limit'];
        $Player['equipa_s_limit'] = $equipaMS['S_limit'];
        $Player['equipa_s_p_jogo_limit'] = $equipaMS['S_p_jogo_limit'];

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
        if($team['Team']['winner']) { return true; } else { return false; }
    }

//AVERAGE RATING



/**
 * saveRating method
 * Salva o rating de um jogador para a tabela de jogadores
 *
 * @param array $team
 * @return bool
 */
    public function saveRating($id = null, $rating = null) {

        $this->id = $id;
        if ($this->exists() && isset($rating)) {
            $this->save(array('Player' => array('ratingLouie' => $rating)));
        }else{
            throw new NotFoundException(__('jogador ou rating inválido'));
        }

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
        $games = $this->Goal->find('all', array('conditions' => array('game_id >=' => $gameId, 'player_id =' => $id),
                                                'order' => array('Goal.id' => 'desc')));


        //nº de jogos com assistências
        $nGames = count($games);
        if($nGames == 0){
            return array('assist' => 0,
                         'assist_p_jogo' => 0,
                         'assist_limit' => 0,
                         'assist_p_jogo_limit' => 0);
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
 * goalsAssists() method
 *
 * @param
 * @return array
 */

    public function goalsAssists($id, $limit) {

        //golos e assistências dos últimos X jogos
        $options = array('conditions' => array('player_id' => $id),
            'order' => array('Goal.id' => 'desc'),
            'limit' => $limit);
        return $this->Goal->find('all', $options);

    }






}
