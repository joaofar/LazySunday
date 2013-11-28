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

        //mean
        $this->set('mean', $this->Rating->find('list', array(
            'fields' => array('Rating.game_id', 'Rating.mean'),
            'conditions' => array('Rating.player_id' => $id),
            'order' => array('Rating.id' => 'desc'),
            'limit' => 20
        )));

        //standard deviation
        $this->set('standardDeviation', $this->Rating->find('list', array(
            'fields' => array('Rating.game_id', 'Rating.standard_deviation'),
            'conditions' => array('Rating.player_id' => $id),
            'order' => array('Rating.id' => 'desc'),
            'limit' => 20
        )));

        //para o gráfico 'rating evo' //diferencial de rating, quanto subiste ou desceste
        // $options = array(
        //     'fields' => array('Goal.game_id', 'Goal.spPts'),
        //     'conditions' => array('Goal.player_id' => $id),
        //     'order' => array('Goal.id' => 'desc'),
        //     'limit' => 20
        // );

        // $this->set('difEvo', $this->Goal->find('list', $options));

        //para o gráfico 'Diferença de Golos' (mostra vitórias e derrotas por diferença de golos)
        $this->set('winLoseStats', $this->Game->winLoseStats($id));

        //para o gráfico 'Golos e Assistências'
        $this->set('goals', $this->Player->goalsAssists($id, Configure::read('limit')));
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
        $players['n_min_pre'] = Configure::read('n_min_pre');

        //ranking TrueSkill
        $players['trueSkill'] = $this->Rating->rankingList(20);

        foreach ($players['trueSkill'] as $key => $player) {
            $players['trueSkill'][$key]['tristate'] = $this->Team->tristate($player['id'], 7);
        }


        //topGoalscorer
        $op_topGoalscorer = array('order' => array('Player.golos_p_jogo' => 'desc', 'Player.presencas' => 'desc'),
            'conditions' => array('Player.presencas >=' => $players['n_min_pre']));
        $players['topGoalscorer'] = $this->Player->find('first', $op_topGoalscorer);

        //topAssists
        $op_topAssists = array('order' => array('Player.assist_p_jogo' => 'desc', 'Player.presencas' => 'desc'),
            'conditions' => array('Player.presencas >=' => $players['n_min_pre']));
        $players['topAssists'] = $this->Player->find('first', $op_topAssists);

        //offensiveInfluence
        $op_offensive = array('order' => array('Player.equipa_m_p_jogo' => 'desc', 'Player.presencas' => 'desc'),
            'conditions' => array('Player.presencas >=' => $players['n_min_pre']));
        $players['offensiveInfluence'] = $this->Player->find('first', $op_offensive);

        //defensiveInfluence
        $op_defensive = array('order' => array('Player.equipa_s_p_jogo' => 'asc'),
            'conditions' => array('Player.presencas >=' => $players['n_min_pre']));
        $players['defensiveInfluence'] = $this->Player->find('first', $op_defensive);

        //allGoals
        $goals = $this->Goal->find('all');
        $players['allGoals'] = 0;
        foreach ($goals as $goal) {
            $players['allGoals'] += $goal['Goal']['goals'];
        }

        //nGames
        $players['nGames'] = $this->Game->gameCount();

        return $players;
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
 * updateStats method
 *
 * @param string $id
 * @return array
 */
    public function updateStats($id) {
        if($id == 'all'){
        $this->set('updateStats', $this->Player->updateStats_allPlayers());
        }
        else{
        $this->set('updateStats', $this->Player->updateStats($id));
        }
    }

/**
 * averageRating method
 *
 * @param string $id
 * @return array
 */
    public function averageRating($id) {

        if($id == 'all'){
            $this->set('averageRating', $this->Player->averageRating_allPlayers());
        }
        else{
            $this->set('averageRating', $this->Player->averageRating($id));
        }

    }

/**
 * teste method
 *
 * @param string $id
 * @return array
 */
    public function teste()
    {
    $this->set('teste', $this->Invite->getInvited(11));
    }
}
