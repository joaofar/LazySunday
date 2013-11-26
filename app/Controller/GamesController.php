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
		$this->set('game', $this->Game->read(null, $id));

        $game = $this->Game->findById($id);

        if($game['Game']['estado'] == 0) {
        //Teams
        $this->set('generatedTeams', $this->Team->generate($id, $this->Invite->invites($id)));
        }
        elseif($game['Game']['estado'] == 1) {
        }
        else {
        //teams goals - variaveis para a view
        $this->set('info', $this->Game->info($id));
        }

        //top info
        $this->set('n_games', $this->Game->gameCount());
        $this->set('id', $id);

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
        /*if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }*/
        $this->Game->id = $id;
        if (!$this->Game->exists()) {
            throw new NotFoundException(__('Invalid game'));
        }

        //Procurar este jogo e os modelos associados
        $game = $this->Game->find('first', array('conditions' => array('Game.id' => $id), 'recursive' => 1));

        //Apagar equipas
        foreach($game['Team'] as $team){
            $this->Team->delete($team['id']);
        }
        //Apagar invites
        foreach($game['Invite'] as $invite){
            $this->Invite->delete($invite['id']);
        }
        //Apagar golos
        foreach($game['Goal'] as $goal){
            $this->Goal->delete($goal['id']);
        }

        //Apagar o jogo
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

    public function teste($id) {

        // $this->set('teste', $this->rateAllGames());
        $this->set('teste', $this->Game->info($id));
        // $this->set('teste', $this->Rating->ratingExists(21, 11));
    }

    public function rateAllGames() {
        //array com todos os jogos
        $games = $this->Game->find('all');

        foreach($games as $game){
            $this->rateGame($game['Game']['id']);
        }

    }

/**
 * rankGames
 * faz o rating de cada jogador no jogo seleccionado, usando o sistema trueskill
 * O rating final, é o rating no final do jogo.
 *
 * @param int $id [game_id]
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
    }


     public function trueSkill($teams)
     {
        $trueSkill = new TrueSkill($teams);
        return $trueSkill->getRatings();
     }



}
