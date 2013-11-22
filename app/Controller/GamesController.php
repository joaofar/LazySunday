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
        //Invites - variaveis para a view
        //$this->set($this->Invite->invites($id));
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
        //$this->set('list_games', array_reverse($this->Game->find('list'), true));

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

    public function teste() {

        $TrueSkill = new TwoPlayerTrueSkillCalculator();
        //$TrueSkill = new teamRating();

        $this->set('stats', $TrueSkill);
    }

    public function eloAll() {


        //array com todos os jogadores
        $games = $this->Game->find('all');

        foreach($games as $game){
            $this->eloRating($game['Game']['id']);
        }

    }

/**
 * playerPoints_elo
 * faz o rating de cada jogador no jogo seleccionado, usando o sistema elo para equipas
 * O rating final, é o rating no final do jogo.
 *
 * @param array $team
 * @return bool
 */

    public function eloRating($id) {
        //instanciar a classe teamRating que calcula perdas e ganhos entre equipas de 5 jogadores
        $teamRating = new teamRating;

        //procurar as equipas deste jogo
        $teams = $this->Team->find('all', array('conditions' => array('Team.game_id' => $id), 'recursive' => 1));

        $i = 0;
        foreach($teams as $team){

            if($team['Team']['winner'] == 1){
                $winner = 1;
            }else{
                $winner = 0;
            }

            foreach($team['Goal'] as $player){

                //rating deste jogador na altura deste jogo
                //procurar o último jogo do jogador que é o segundo item neste array
                $previousGame = $this->Goal->find('all', array('conditions' => array('Goal.game_id <' => $player['game_id'], 'Goal.player_id' => $player['player_id']),
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

                $teamArray[$i][] = array(
                    'id' => $player['player_id'],
                    'goal_id' => $player['id'],
                    'winner' => $winner,
                    'elo' => $player['curr_rating']
                );
            }
            $i++;
        }

        //usar a class teamRating para calcular quanto é que cada jogador sobe ou desce
        $teamRating->setTeams($teamArray[0], $teamArray[1]);
        $teamsWithRating = $teamRating->calculate();

        debug($teamsWithRating);

        //salvar para a tabela golos
        foreach($teamsWithRating as $key => $team){

            if($key == 'teamA'){
                $winProbability = $teamRating->getTeamAWinRate() * 100;
            }elseif($key == 'teamB'){
                $winProbability = $teamRating->getTeamBWinRate() * 100;
            }

            foreach($team as $player){

                if($player['winner'] == 1){
                    $playerPoints =  $player['elo'] + $player['gain'];
                    $gainLoss = $player['gain'];
                }elseif($player['winner'] == 0){
                    $playerPoints =  $player['elo'] - $player['loss'];
                    $gainLoss = -$player['loss'];
                }

                $pointsSave = array(
                    'Goal' => array(
                        'player_points' => $playerPoints,
                        'curr_rating' => $player['elo'],
                        'peso' => $winProbability,
                        'spPts' => $gainLoss,
                        'basePts' => 0
                    )
                );

                //debug($playerPoints);
                //save Goal
                $this->Goal->id = $player['goal_id'];
                $this->Goal->save($pointsSave);

                $playerList[]=$pointsSave;

                //actualizar o rating para a tabela de jogadores, para este jogador
                $this->Player->saveRating($player['id'], $playerPoints);
            }





        }
        $this->set('stats', $playerList);
    }


     public function trueSkill() 
     {
        //teams
        $teamWinner = array();
        $teamLoser = array();

        //calculator
        $trueSkill = new TrueSkill($teamWinner, $teamLoser);
        $this->set('trueSkill', $trueSkill->teste());
     }



}
