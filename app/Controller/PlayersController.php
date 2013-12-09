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
            //return true;
            

        }
    }



/**
 * index method
 *
 * @return void
 */
	public function index($nPre = 0) {
        
        $this->paginate = array(
            'conditions' => array('Player.games_played >=' => $nPre),
            'contain' => array(
                'Rating.mean',
                'Rating.limit' => 1)
            );
        

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
     * @param int $id
     * @return void
     */
    public function saveTeams($id) {
        // fetch genereated teams
        $teams = $this->Team->generate($id, $this->Invite->get($id, 'invited'));

        // check if there are 10 players who said yes, otherwise exit
        if($teams['lineUpStatus'] != 10){
            throw new ForbiddenException(__('Só podes gravar equipas com 10 jogadores'));
        }

        // PREPARE save arrays with the correct structure
        // using the saveAll method (cakephp: saveMany + saveAssociated) 
        foreach ($teams['teams'] as $key => $team) {
            // array for games_players
            $saveGame['Game']['id'] = $id;
            // array for players_teams
            $saveTeam[$key]['Team'] = array(
                'id' => $team['Team']['id'], 
                'rating' => $team['Team']['rating']
                );
            // same for players
            foreach ($team['Player'] as $player) {
                $saveGame['Player'][]['player_id'] = $player['id'];
                $saveTeam[$key]['Player'][]['player_id'] = $player['id'];
            }
        }
        // SAVE
        // with this array structure we can populate the joins with this command: saveAll()
        // note that the correct structure is ['Player'][$i]['player_id'] and not ['Player'][$i]['id']
        if (!$this->Game->saveAll($saveGame)){ return false; }
        if (!$this->Team->saveAll($saveTeam)){ return false; }

        //update game stage
        $this->Game->id = $id;
        $this->Game->set('stage', 'roster_closed');
        if (!$this->Game->save()) { return false; }

        //redirect
        $this->redirect(array('controller' => 'Games', 'action' => 'submitScore', $id));
    }

/**
 * sidebarStats method
 *
 * @param string $id
 * @return array
 */
    public function sidebarStats() {
        //min games_played
        $players['n_min_pre'] = Configure::read('n_min_pre');

        //ranking TrueSkill
        $players['trueSkill'] = $this->Rating->rankingList(20);

        foreach ($players['trueSkill'] as $key => $player) {
            $players['trueSkill'][$key]['tristate'] = $this->Team->tristate($player['id'], 7);
        }


        //topGoalscorer
        $op_topGoalscorer = array('order' => array('Player.goals_average' => 'desc', 'Player.games_played' => 'desc'),
            'conditions' => array('Player.games_played >=' => $players['n_min_pre']));
        $players['topGoalscorer'] = $this->Player->find('first', $op_topGoalscorer);

        //topAssists
        $op_topAssists = array('order' => array('Player.assists_average' => 'desc', 'Player.games_played' => 'desc'),
            'conditions' => array('Player.games_played >=' => $players['n_min_pre']));
        $players['topAssists'] = $this->Player->find('first', $op_topAssists);

        //offensiveInfluence
        $op_offensive = array('order' => array('Player.team_scored_average' => 'desc', 'Player.games_played' => 'desc'),
            'conditions' => array('Player.games_played >=' => $players['n_min_pre']));
        $players['offensiveInfluence'] = $this->Player->find('first', $op_offensive);

        //defensiveInfluence
        $op_defensive = array('order' => array('Player.team_conceded_average' => 'asc'),
            'conditions' => array('Player.games_played >=' => $players['n_min_pre']));
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
    public function teste($id)
    {
    $this->set('teste', $this->Invite->getInvited($id));
    }
}
