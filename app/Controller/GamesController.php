<?php
App::uses('AppController', 'Controller');
//App::import('Vendor', 'teamRating');
App::import('Vendor', 'Moserware/TrueSkill');

/**
 * Games Controller
 *
 * @property Game $Game
 */
class GamesController extends AppController {


    public $helpers = array('Time');

    public function beforeFilter(){
        //$teamRating = new teamRating();
    }

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
        //title info
        $this->set('n_games', $this->Game->gameCount());
        $this->set('id', $id);
		$this->set('game', $this->Game->read(null, $id));

        $game = $this->Game->findById($id);

        if($game['Game']['estado'] == 0) {
        //Teams
        $this->set('generatedTeams', $this->Team->generate($id, $this->Invite->get($id, 'invited')));
        } else {
        //Game details
        $this->set('details', $this->Game->details($id));
        }

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
                        // $this->Session->setFlash(__('The game has been saved'));
                    } else {
                        // $this->Session->setFlash(__('The game could not be saved. Please, try again.'));
                    }
                }
            }
            $this->redirect(array('controller' => 'Games', 'action' => 'view', $gameid['Game']['id']));
        }

        
        $players = $this->Player->find('list', array('order' => array('Player.conv' => 'asc')));
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
                // $this->Session->setFlash(__('The game has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                // $this->Session->setFlash(__('The game could not be saved. Please, try again.'));
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
        /*if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }*/
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
        $options = array('conditions' => array('Team.game_id' => $id), 'recursive' => 1);
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
 * percentdist method
 *
 * @param string $id
 * @return array
 */

    public function percentdist() {

        $percentDist = $this->Game->percentDist();
        $this->set('stats', $percentDist);
    }

/**
 * playerPoints method
 *
 * @param string $id
 * @return array
 */

    public function playerPoints($id) {

        if($id == 'all'){
            $this->set('playerPoints', $this->Game->playerPoints_allGames());
        }else{
            $this->set('playerPoints', $this->Game->playerPoints_new($id));
        }

    }


/**
 * teste method
 *
 * @param string $id
 * @return array
 */

    public function teste() {


        $data = array(
            'Game' => array('team_a' => 24, 'team_b' => 42, 'estado' => 66),
            'Player' => array('id' => 20)
            );

        // $this->Game->saveAll($data);

        // $this->set('teste', $this->rateAllGames());
        // $this->Session->setFlash(__('teste'));
        // $this->set('teste', $this->Invite->invites($id));
        // $this->set('teste', $this->Rating->ratingExists(21, 11));
        // $this->set('teste', $this->Game->saveAll($data));
    }

    public function rateAllGames() {
        //array com todos os jogos
        $games = $this->Game->find('all');

        foreach($games as $game){
            $this->rateGame($game['Game']['id']);
        }
    }

/**
 * rateGame
 * faz o rating de cada jogador no jogo seleccionado, usando o sistema trueskill
 * O rating final, é o rating no final do jogo.
 *
 * @param int $id game_id
 */

    public function rateGame($id) {
        //procurar as equipas deste jogo
        $teams = $this->Team->find('all', array('conditions' => array('Team.game_id' => $id), 'recursive' => 1));

        //criar arrays 'teamWinner' e 'teamLoser' para o calculo do rating
        foreach($teams as $team){
            if ($team['Team']['is_winner'] == 1) {
                foreach ($team['Player'] as $player) {
                    //vai buscar o rating mais recente deste jogador
                    $currentRating = $this->Rating->get($player['id'], $id);

                    $teamWinner[] = array(
                        'id' => $player['id'],
                        'team_id' => $team['Team']['id'],
                        'mean' => $currentRating['mean'],
                        'standard_deviation' => $currentRating['standard_deviation']
                        );
                }
            } else {
                foreach ($team['Player'] as $player) {
                    $currentRating = $this->Rating->get($player['id'], $id);

                    $teamLoser[] = array(
                        'id' => $player['id'],
                        'team_id' => $team['Team']['id'],
                        'mean' => $currentRating['mean'],
                        'standard_deviation' => $currentRating['standard_deviation']
                        );
                }
            }
        }

        //Calcular o novo rating
        //devolve uma array com os ratings novos
        $newRatings = $this->trueSkill(array($teamWinner, $teamLoser));

        //salvar o rating
        foreach ($newRatings as $player) {

            $ratingExists = $this->Rating->ratingExists($player['id'], $id);

            if (!$ratingExists) {
                //cria um novo
                $this->Rating->create();
            } else {
                //usa o que já existe
                $this->Rating->id = $ratingExists;
            }

            //SAVE
            $this->Rating->save(array('Rating' => array(
                'game_id' => $id,
                'team_id' => $player['team_id'],
                'player_id' => $player['id'],
                'mean' => $player['mean'],
                'standard_deviation' => $player['standard_deviation'])));
        }

        $this->redirect(array('controller' => 'Games', 'action' => 'view', $id));
    }

/**
 * trueSkill method
 * @param  array $teams array com 2 equipas e respectivos jogadores [mean e standard deviation]
 * @return array        ratings actualizados
 */
     public function trueSkill($teams)
     {
        $trueSkill = new TrueSkill($teams);
        return $trueSkill->getRatings();
     }

/**
 * submitScore method
 * 
 * grava os resultados de um jogo na base de dados
 * @param int $id game_id
 * @return void
 */
    public function submitScore($id) {

        // save Goals/Assists
        if (isset($this->request->data['Goal'])) {
            // save all goals and assists
            if(!$this->Goal->saveMany($this->request->data['Goal'])){
                return false;
            }
            
            //prepare team data
            foreach ($this->request->data['Goal'] as $player) {
                $teamGoals[$player['team_id']][] = $player['goals'];
            }

            foreach ($teamGoals as $teamId => $goals) {
                $this->request->data['Team'][] = array(
                    'id' => $teamId, 
                    'score' => array_sum($goals)
                    );
            }
        }

        // update Teams score/is_winner
        if (isset($this->request->data['Team'])) {
            
            // descobrir qual a equipa vencedora
            if($this->request->data['Team'][0]['score'] > $this->request->data['Team'][1]['score']) {
                $this->request->data['Team'][0]['is_winner'] = 1;
                $this->request->data['Team'][1]['is_winner'] = 0;
            } else {
                $this->request->data['Team'][0]['is_winner'] = 0;
                $this->request->data['Team'][1]['is_winner'] = 1;
            }
                
            if (!$this->Team->saveMany($this->request->data['Team'])){
                 return false;
            }
            
        }

        // change game state to 2
        $this->Game->id = $id;
        $this->Game->save(array('estado' => 2, 
            'team_a' => $this->request->data['Team'][0]['score'], 
            'team_b' => $this->request->data['Team'][1]['score']
            ));

        // rate Game
        $this->rateGame($id);
    }




}
