<?php
App::uses('AppController', 'Controller');
/**
 * Games Controller
 *
 * @property Game $Game
 */
class GamesController extends AppController {


    public $helpers = array('Time');



/**
 * index method
 *
 * @return void
 */
	public function index() {
		$games = $this->Game->find('all', array('order' => array('Game.id' => 'desc')));
		$this->set('games', $games);
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {

        $this->Game->id = $id;
		if (!$this->Game->exists()) {
			throw new NotFoundException(__('Invalid game'));
		}
		$this->set('game', $this->Game->read(null, $id));

        $game = $this->Game->findById($id);

        if($game['Game']['estado'] == 0) {
        //Invites - variaveis para a view
        $this->set($this->Invite->invites($id));
        //Teams
        $this->set('generatedTeams', $this->Team->generate($id, $this->Invite->invites($id)));
        }
        elseif($game['Game']['estado'] == 1) {
        }
        else {
        //teams goals - variaveis para a view
        $this->set($this->Game->teamsGoals($id));
        }
        //menu dos jogos à esquerda
        $this->set('list_games', array_reverse($this->Game->find('list'), true));

        $this->set('n_games', $this->Game->gameCount());
        $this->set('id', $id);
        //rebuild player stats
        //$this->presencas();

	}





    /**
     * add method
     *
     * @return void
     */
    public function add() {
        if ($this->request->is('post')) {

            $savegame['Game'] = array_slice($this->request->data['Game'], 0, 1);
            $savegame['Game']['estado'] = 0;

            $saveplayers = array_slice($this->request->data['Game'], 1);

            $this->Game->create();
            $gameid = $this->Game->save($savegame);

            foreach($saveplayers as $key => $player) {
                $saveplayer = array('Invite' => array(
                    'game_id' => $gameid['Game']['id'],
                    'player_id' => str_replace('jogador', '', $key)
                ));
                if($player) {
                    $this->Invite->Create();
                    if($this->Invite->save($saveplayer)) {
                        $this->Session->setFlash(__('The game has been saved'));
                    } else {
                        $this->Session->setFlash(__('The game could not be saved. Please, try again.'));
                    }
                }
            }
            $this->redirect(array('controller' => 'Games', 'action' => 'view', $gameid['Game']['id']));
        }

        $options = array('order' => array('Player.conv' => 'asc'));
        $players = $this->Player->find('list', $options);
        $this->set(compact('players'));
    }

    /**
     * edit method
     *
     * @param string $id
     * @return void
     */
    public function edit($id = null) {
        $this->Game->id = $id;
        if (!$this->Game->exists()) {
            throw new NotFoundException(__('Invalid game'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Game->save($this->request->data)) {
                $this->Session->setFlash(__('The game has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The game could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->Game->read(null, $id);
        }
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
        $this->Game->id = $id;
        if (!$this->Game->exists()) {
            throw new NotFoundException(__('Invalid game'));
        }
        if ($this->Game->delete()) {
            $this->Session->setFlash(__('Game deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Game was not deleted'));
        $this->redirect(array('action' => 'index'));
    }

/**
 * admin method
 *
 * @param string $id
 * @return void
 */
    public function admin($id = null) {
        $this->Game->id = $id;
        if (!$this->Game->exists()) {
            throw new NotFoundException(__('Invalid game'));
        }
        $this->set('game', $this->Game->read(null, $id));

        //Invites - variaveis para a view
        $this->set($this->Invite->invites($id));

        //submitGoals
        //Find Teams
        $options = array('conditions' => array('Team.game_id' => $id));
        $teams = $this->Team->find('all', $options);
        $this->set('teams', $teams);
    }



/**
 * gameSheet method
 *
 * @param string $id
 * @return void
 */
    public function gs($id = null) {

        //generatedTeams
        $this->set('generatedTeams', $this->Team->generate($id, $this->Invite->invites($id)));

        $this->layout = 'gs'; //this will use the pdf.ctp layout
        $this->render();
    }



    public function teamIdtoGoal() {
        $this->Game->teamIdtoGoal();
    }

    public function allPlayerPoints() {
        $this->Game->allPlayerPoints();
    }

/**
 * teste method
 *
 * @param string $id
 * @return array
 */

    public function teste() {
        //debug($this->Player->countPresencas(21,10));
        //debug($this->Player->bestGoalAverage(true));
        //debug($this->Player->gameRating(56));

        //$teste = $this->Player->averageRating(15);
        //$teste = $this->Player->allAverageRating();

        //$this->set('teste', $teste);

        //$teste = $this->Game->playerPointsAvg_lastX(20);
        $teste = $this->Game->percentDist();
        $this->set('stats', $teste);
    }

/**
 * teste method
 *
 * @param string $id
 * @return array
 */

    public function percentdist() {

        $percentDist = $this->Game->percentDist();
        $this->set('stats', $percentDist);
    }


}
