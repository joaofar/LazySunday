<?php
App::uses('AppController', 'Controller');
/**
 * Players Controller
 *
 * @property Player $Player
 */
class PlayersController extends AppController {

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
        $this->set('goalsAssists', $this->Player->goalsAssists($id, Configure::read('limit')));

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
                // Salva a ordem da convocatória igual ao id 
                // para evitar que jogadores novos apareceçam no inicio das listas
                $this->Player->saveField('conv', $this->Player->id);
                // $this->Player->save();

                // Se o save for bem sucedido, criar um rating inicial para o jogador
                $this->Rating->create();
                $rating = array('Rating' => array(
                    'player_id' => $this->Player->id,
                    'mean' => 5,
                    'standard_deviation' => 1.666));
                if ($this->Rating->save($rating)) {
                    $this->Session->setFlash(__('The player has been saved'));
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->setFlash(__('Player rating could not be saved. Please, try again.'));
                }				
			} else {
				$this->Session->setFlash(__('Player could not be saved. Please, try again.'));
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

        //min games_player necessary for a x player ranking list
        $n_min_pre_list_size = $this->Player->n_min_pre_list_size(10);

        //correcting the variable if necessary
        if($n_min_pre_list_size < $players['n_min_pre']) {
           $players['n_min_pre'] = $n_min_pre_list_size;
        }

        //ranking TrueSkill
        $players['trueSkill'] = $this->Rating->rankingList($players['n_min_pre']);

        foreach ($players['trueSkill'] as $key => $player) {
            $players['trueSkill'][$key]['tristate'] = $this->Team->tristate($player['id'], 6);
        }


        //topGoalscorer
            $op_topGoalscorer = array('order' => array('Player.goals_average_limit' => 'desc'),
                'conditions' => array('Player.games_played >=' => $players['n_min_pre'], 'Player.goals_average_limit !=' => 0),
                'limit' => 10);
            $topGoalscorer = $this->Player->find('all', $op_topGoalscorer);
            
            //check if an average exists
            if (count($topGoalscorer) != 0) {
                //check if player is idle, if true go to next candidate
                while($this->Player->idle($topGoalscorer[0]['Player']['id'])) {
                    array_shift($topGoalscorer);
                }

                $players['topGoalscorer'] = $topGoalscorer[0];  
             } else {
                //in case there is no average
                $players['topGoalscorer']['Player'] = array('name' => '', 'goals_average_limit' => '');
             }
             
      
      
        //topAssists
            $op_topAssists = array('order' => array('Player.assists_average_limit' => 'desc'),
                'conditions' => array('Player.games_played >=' => $players['n_min_pre'], 'Player.assists_average_limit !=' => 0),
                'limit' => 10);
            $topAssists = $this->Player->find('all', $op_topAssists);

            //check if an average exists
            if (count($topAssists) != 0) {
                //check if player is idle, if true go to next candidate
                while($this->Player->idle($topAssists[0]['Player']['id'])) {
                array_shift($topAssists);
                }

                $players['topAssists'] = $topAssists[0];   
             } else {
                //in case there is no average
                $players['topAssists']['Player'] = array('name' => '', 'assists_average_limit' => '');
             }
  
        //offensiveInfluence
            $op_offensive = array('order' => array('Player.team_scored_average_limit' => 'desc'),
                'conditions' => array('Player.games_played >=' => $players['n_min_pre'], 'Player.team_scored_average_limit !=' => 0),
                'limit' => 10);
            $offensiveInfluence = $this->Player->find('all', $op_offensive);

            //check if player is idle, if true go to next candidate
           while($this->Player->idle($offensiveInfluence[0]['Player']['id'])) {
                array_shift($offensiveInfluence);
            }

            $players['offensiveInfluence'] = $offensiveInfluence[0]; 


        //defensiveInfluence   
            $op_defensive = array('order' => array('Player.team_conceded_average_limit' => 'asc'),
                'conditions' => array('Player.games_played >=' => $players['n_min_pre'], 'Player.team_conceded_average_limit !=' => 0),
                'limit' => 10);
            $defensiveInfluence = $this->Player->find('all', $op_defensive);

            //check if player is idle, if true go to next candidate
           while($this->Player->idle($defensiveInfluence[0]['Player']['id'])) {
                array_shift($defensiveInfluence);
            }

            $players['defensiveInfluence'] = $defensiveInfluence[0]; 
        

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
    public function updateStats($id, $limit) {
        if($id == 'all'){
        $this->set('updateStats', $this->Player->updateStats_allPlayers());
        }
        else{
        $this->set('updateStats', $this->Player->updateStats($id, $limit));
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
    $this->set('teste', $this->Rating->get(105, 297));
    }


}
