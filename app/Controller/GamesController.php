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
		// $this->set('games', $this->Game->find('all', array(
  //           'order' => array('Game.id' => 'desc'),
  //           'contain' => array('Team.score')
  //           )));
        $this->paginate = array(
            'order' => array('id' => 'DESC'),
            'contain' => array('Team.score'),
            'limit' => 10
            );

        $this->set('games', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
    //check if stage is correct
        $this->isStage($id, 'view');

        $this->Game->id = $id;
		if (!$this->Game->exists()) {
			throw new NotFoundException(__('Invalid game'));
		}       
        //title info
        $this->set('n_games', $this->Game->gameCount());
        $this->set('id', $id);
		$this->set('game', $this->Game->read(null, $id));

        $game = $this->Game->findById($id);

        //Game details
        $this->set('details', $this->Game->details($id));

	}

/**
 * add method
 *
 * @return void
 */
    public function add() {
        // POST
        if ($this->request->is('post')) {

            // create a game
            $this->Game->create();
            if (!$this->Game->save($this->request->data)) {
                $this->Session->setFlash(__('error creating game'));
            }

            // prepare invites
            foreach($this->request->data['Invite'] as $key => $player) {
                
                if ($player['value']) {
                    // se a checkbox tiver sido cruzada adiciona-se o game_id e fica pronto para ser salvo
                    $this->request->data['Invite'][$key]['game_id'] = $this->Game->id;
                } else {
                    // caso contrário retira-se esse jogador da array
                    unset($this->request->data['Invite'][$key]);
                }
            }

            // save invites
            if ($this->Invite->saveMany($this->request->data['Invite'])) {
                $this->Session->setFlash(__('jogo criado com sucesso'));
            } else {
                $this->Session->setFlash(__('erro a salvar os invites'));
            }

            // redirect
            $this->redirect(array(
                'action' => 'view', 
                $this->Game->id
                ));
        }

        // VIEW
        $players = $this->Player->find('all', array(
            'order' => array('conv' => 'asc'),
            'fields' => array('id', 'name'),
            'contain' => array(
                'Rating.mean',
                'Rating.limit' => 1)
            ));

        $this->set('players', $players);
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
 * new method
 *
 * 
 * @param  int $id
 * @return [type]     [description]
 */
    public function roster($id)
    {   
        //check if stage is correct
        $this->isStage($id, 'roster');
        $this->set('game', $this->Game->findById($id));
        $this->set('generatedTeams', $this->Team->generate($id, $this->Invite->get($id, 'invited')));
    }

    public function roster_closedTeams($id)
    {
        $teams = $this->Team->find('all', array(
            'conditions' => array('game_id' => $id),
            'fields' => array('id'),
            'contain' => array(
                'Player.fields' => array('id', 'name'),
                'Player' => array(
                    'Rating' => array(
                        'limit' => 1, 
                        'fields' => array('mean')
                        )
                    )
                )
            )
        );

        //team value
        foreach ($teams as $t_key => $team) {
            $teams[$t_key]['Team']['value'] = 0;
            foreach ($team['Player'] as $player) {
                $teams[$t_key]['Team']['value'] += $player['Rating'][0]['mean'];
            }
        }    

        return $teams;
    }

    public function roster_closed($id)
    {
        $this->isStage($id, 'roster_closed');

        $teams = $this->roster_closedTeams($id);
        $this->set(compact('teams'));

        

        if ($this->request->is('post')) {
            foreach ($teams as $key => $team) {
                // move player to new team
                if ($team['Team']['id'] != $this->request->data['Team']['id']) {
                    unset($save);
                    // team id
                    $save['Team']['id'] = $team['Team']['id'];  
                    // existing team players 
                    // this array has to have this format in order for the save to work:
                    // http://patisserie.keensoftware.com/en/pages/how-to-save-habtm-data-in-cakephp
                    foreach ($team['Player'] as $player) {
                        $save['Player']['Player'][] = $player['id'];
                    }
                    // add changed player
                    $save['Player']['Player'][] = $this->request->data['Player']['id'];

                    // save team
                    $this->Team->save($save);

                // remove player from his current team
                } else {
                    unset($save);
                    $save['Team']['id'] = $team['Team']['id'];  

                    foreach ($team['Player'] as $player) {
                        if ($player['id'] != $this->request->data['Player']['id']) {
                            $save['Player']['Player'][] = $player['id'];
                        }
                    }

                    // save team
                    $this->Team->save($save);
                }
            }
            // refresh teams after update    
            $this->set('teams', $this->roster_closedTeams($id));
        }
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

        //lista de jogadores não convidados
        $this->set($this->Invite->get($id, 'not_invited'));

        //submitGoals
        //Find Teams
        

        // sidebar menu
        $sidebarMenu = array(
            $this->sidebarMenuItem('folha de jogo', 'Games', 'gs', $id),
            $this->sidebarMenuItem('enviar emails', 'Invites', 'sendEmails', $id),
            $this->sidebarMenuItem('gravar equipas', 'Players', 'saveTeams', $id),
            $this->sidebarMenuItem('apagar jogo', 'Games', 'delete', $id),
            $this->sidebarMenuItem('voltar à convocatória', 'Games', 'roster', $id),
            $this->sidebarMenuItem('alterar equipas', 'Games', 'roster_closed', $id),
            $this->sidebarMenuItem('colocar resultado', 'Games', 'submitScore', $id)
            );

        $this->set('sidebarMenu', $sidebarMenu);
    }

/**
 * isStage method
 *
 * validação da acção (roster, closed, view) em relação ao estado do jogo
 * caso não esteja certa, o utilizador é redireccionado para a accção correcta
 * @param  int  $id    
 * @param  string  $stage
 * @return void
 */
    public function isStage($id, $stage)
    {
        $game = $this->Game->findById($id);
        if ($game['Game']['stage'] !== $stage) {
            $this->redirect(array('action' => $game['Game']['stage'], $id));    
        }
    }

/**
 * gameSheet method
 *
 * @param string $id
 * @return void
 */
    public function gs($id = null) {

        //generatedTeams
        $this->set('teams', $this->Team->generate($id, $this->Invite->get($id, 'invited')));

        $this->layout = 'gs'; //this will use the gs.ctp layout
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
 * teste method
 *
 * @param string $id
 * @return array
 */

    public function teste() {
        $data = array(
            'Team' => array(
                'id' => 229
                ),
            'Player' => array(
                'Player' => array(20, 14, 17, 21, 42)
                ));

        $this->set('teste', $this->Team->save($data));
    }

    public function rateAllGames() {
        //array com todos os jogos
        $games = $this->Game->find('all');

        foreach($games as $game){
            $this->rateGame($game['Game']['id']);
        }
    }

public function cssTest()
{
    
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
    public function submitScore($id)
    {
        $this->isStage($id, 'roster_closed');

        if ($this->request->is('post')) {
            // save score
            if ($this->Game->submitScore($this->request->data)){

                // change game state to 'view' (game over)
                $this->Game->id = $id;
                $this->Game->save(array('stage' => 'view'));

                // rate Game
                $this->rateGame($id);
            }
        } else {
            // set view variables
            $this->set('game', $this->Game->findById($id));
            $this->set('teams', $this->Team->find('all', array(
                'conditions' => array('Team.game_id' => $id), 
                'recursive' => 1)));
        }
    }




}
