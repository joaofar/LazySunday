<?php
App::uses('AppController', 'Controller');
/**
 * Players Controller
 *
 * @property Player $Player
 */
class PlayersController extends AppController {

    public function beforeFilter() {

        if ($this->action == 'view')
        {
            //$this->Player->setPlayersRankingEvo();

        }
    }
/**
 * index method
 *
 * @return void
 */
	public function index($nPre = null) {

        if($nPre != null){
            $this->paginate = array('conditions' => array('Player.presencas >=' => $nPre));
        }

		$this->set('players', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Player->id = $id;
		if (!$this->Player->exists()) {
			throw new NotFoundException(__('Invalid player'));
		}
		$this->set('player', $this->Player->read(null, $id));

        //para os gráficos 'rating Evo' e 'Pontos por Jogo'
        $this->set('playerEvo', $this->Player->playerPointsAvg_lastX($id));
        //$this->set('allPlayers', $this->Player->allPLayers());

        //para o gráfico 'Diferença de Golos' (mostra vitórias e derrotas por diferença de golos)
        $this->set('winLoseStats', $this->Game->winLoseStats($id));

        //para o gráfico 'Golos e Assistências'
        $this->set('goals', $this->Player->goalsAssists($id, self::N_MIN_PRE));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Player->create();
			if ($this->Player->save($this->request->data)) {
				$this->Session->setFlash(__('The player has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The player could not be saved. Please, try again.'));
			}
		}
		$teams = $this->Player->Team->find('list');
		$this->set(compact('teams'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Player->id = $id;
		if (!$this->Player->exists()) {
			throw new NotFoundException(__('Invalid player'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Player->save($this->request->data)) {
				$this->Session->setFlash(__('The player has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The player could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Player->read(null, $id);
		}
		$teams = $this->Player->Team->find('list');
		$this->set(compact('teams'));
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Player->id = $id;
		if (!$this->Player->exists()) {
			throw new NotFoundException(__('Invalid player'));
		}
		if ($this->Player->delete()) {
			$this->Session->setFlash(__('Player deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Player was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

    /**
     * saveTeams method
     *
     * @param string $id
     * @return array
     */
    public function saveTeams($id) {
        //fetch genereated teams
        $teams = $this->Team->generate($id, $this->Invite->invites($id));

        //debug($teams);

        //check if there are 10 players who said yes, otherwise exit
        if($teams['teams']['available'] != 10){
            throw new ForbiddenException(__('Só podes gravar equipas com 10 jogadores'));
        }

        for($i = 1; $i <= 2; $i++) {
            //team count
            $options = array('conditions' => array('team_id' => $teams['teams']['team_'.$i.'_id']));
            ${'team_'.$i.'_count'} = $this->PlayersTeam->find('count', $options);

            //validation
            if(${'team_'.$i.'_count'} == 0) {
                foreach ($teams['teams']['team_'.$i] as $teamPlayer) {

                    //add player to the join table players_team
                    $this->PlayersTeam->create();
                    $this->PlayersTeam->save(array('PlayersTeam' => array('team_id' => $teams['teams']['team_'.$i.'_id'],
                                                                          'player_id' => $teamPlayer['id'])));

                    //add player to the join table games_players
                    $this->Game->GamesPlayer->create();
                    $this->Game->GamesPlayer->save(array('GamesPlayer' => array('game_id' => $id,
                                                   'player_id' => $teamPlayer['id'])));
                }
            }
        }

        //change game state to 1
        $this->Game->id = $id;
        $this->Game->save(array('Game' => array('estado' => 1)));

        //redirect
        $this->redirect(array('controller' => 'Games', 'action' => 'admin', $id));

    }

/**
 * sidebarStats method
 *
 * @param string $id
 * @return array
 */
    public function sidebarStats() {
        //min presencas
        $players['n_min_pre'] = self::N_MIN_PRE;

        //rating
        $op_rating = array('order' => array('Player.ratingLouie' => 'desc'),
            'conditions' => array('Player.presencas >=' => self::N_MIN_PRE), 'recursive' => 1);
        $players['ratingList'] = $this->Player->find('all', $op_rating);

        //topGoalscorer
        $op_topGoalscorer = array('order' => array('Player.golos_p_jogo' => 'desc', 'Player.presencas' => 'desc'),
            'conditions' => array('Player.presencas >=' => self::N_MIN_PRE));
        $players['topGoalscorer'] = $this->Player->find('first', $op_topGoalscorer);

        //topAssists
        $op_topAssists = array('order' => array('Player.assist_p_jogo' => 'desc', 'Player.presencas' => 'desc'),
            'conditions' => array('Player.presencas >=' => self::N_MIN_PRE));
        $players['topAssists'] = $this->Player->find('first', $op_topAssists);

        //offensiveInfluence
        $op_offensive = array('order' => array('Player.equipa_m_p_jogo' => 'desc', 'Player.presencas' => 'desc'),
            'conditions' => array('Player.presencas >=' => self::N_MIN_PRE));
        $players['offensiveInfluence'] = $this->Player->find('first', $op_offensive);

        //defensiveInfluence
        $op_defensive = array('order' => array('Player.equipa_s_p_jogo' => 'asc'),
            'conditions' => array('Player.presencas >=' => self::N_MIN_PRE));
        $players['defensiveInfluence'] = $this->Player->find('first', $op_defensive);

        //allGoals
        $goals = $this->Goal->find('all');
        $players['allGoals'] = 0;
        foreach ($goals as $goal) {
            $players['allGoals'] += $goal['Goal']['golos'];
        }

        //nGames
        $players['nGames'] = $this->Game->gameCount();

        return $players;
    }

/**
 * updateStats method
 *
 * @param string $id
 * @return array
 */
    public function updateStats() {
        $this->Player->updateStats();
        //$this->redirect(array('action' => 'index'));
    }

/**
 * allAverageRating method
 *
 * Calcula o louie rating para a tabela dos jogadores
 * Tb calcula as assistências
 *
 * @param
 * @return
 */
    public function allAverageRating() {
        $this->Player->allAverageRating();
        $this->Player->allAssists();
    }

/**
 * chart method
 *
 * @param
 * @return
 */
    public function chart() {

        $this->set('players', $this->Player->chart());
    }

/**
 * stats method
 *
 * Faz as stats globais do Lazyfoot
 *
 * @param
 * @return
 */
    public function stats() {

        $this->set('stats', $this->Player->stats());
    }


/**
 * teste method
 *
 * @param string $id
 * @return array
 */
    public function teste() {

        $this->set('teste', $this->Player->updateStats(21));
        //$this->set('teste', $this->Player->equipaMS(30, 20));
    }
}
