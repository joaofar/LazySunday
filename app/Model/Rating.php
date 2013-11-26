<?php
App::uses('AppModel', 'Model');
/**
 * Goal Model
 *
 * @property Player $Player
 * @property Game $Game
 */
class Rating extends AppModel {
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'id';
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'player_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'mean' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'standard_deviation' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'notempty' => array(
				'rule' => array('notempty'),
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
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Game' => array(
			'className' => 'Game',
			'foreignKey' => 'game_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Team' => array(
			'className' => 'Team',
			'foreignKey' => 'team_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Player' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * defaultRating
 * 
 * @var array
 */
	public $defaultRating = array(
		'mean' => 5,
		'standardDeviation' => 3);

/**
 * get method
 * 
 * Devolve o rating mais recente do jogador
 * @param  int $id [player id]
 * @param bolean $previous (caso se queira o rating anterior ao currente)
 * @param  int $gameID
 * @return array
 */
	public function get($id, $gameID = null)
	{
		//verificar se o jogador tem ratings
		if (!$gameID) {
			//se não houver gameID, devolve o mais recente
			$rating = $this->find('first', array(
			'conditions' => array('Rating.player_id' => $id),
			'order' => array('id' => 'desc')
			));
		} else {
			$rating = $this->find('first', array(
			'conditions' => array('Rating.player_id' => $id, 'Rating.game_id <' => $gameID),
			'order' => array('id' => 'desc')
			));
		}

		if (!$rating) {
			//se não tiver, cria um novo com valores default
			$rating = array(
				'player_id' => $id,
                'mean' => $this->defaultRating['mean'],
                'standard_deviation' => $this->defaultRating['standardDeviation']
                );

            return $rating;
		}else{
			//se tiver, devolve a última rating
			return $rating['Rating'];
		}
	}

/**
 * getPrevious
 *
 * devolve o rating do vizinho inferior ao argumento passado
 * @param  int $id ratingId
 * @return array
 */
	public function getPrevious($id, $playerId)
	{
		$this->id = $id;
		if (!$this->exists()) {
			throw new NotFoundException(__('Rating Inválido'));
		}

 		$previous = $this->find('first', array(
		'conditions' => array('Rating.player_id' => $playerId,'Rating.id <' => $id),
		'order' => array('Rating.id' => 'desc')
		));

		if(!$previous){
			//se não existir um rating anteriordevolve o rating default
			return $this->defaultRating;
		}else{
			return $previous['Rating'];
		}
		
	}

/**
 * ratingExists method
 * 
 * @param  int $id     player_id
 * @param  int $gameId
 * @return int         devolve o id do rating se existir ou falso se não existir
 */
	public function ratingExists($id, $gameId)
	{
		$rating = $this->find('first', array(
			'conditions' => array(
				'Rating.player_id' => $id,
				'Rating.game_id' => $gameId
				)
			));

		if($rating){
			return $rating['Rating']['id'];
		}else{
			return false;
		}
	}

/**
 * rankingList method
 * 
 * (para a sidebar)
 * @param  int $nMinPre
 * @return array
 */
	public function rankingList($nMinPre = null)
	{
		$playersList = $this->Player->find('list', array(
            'fields' => array('Player.id', 'Player.name'),
            'conditions' => array('Player.presencas >=' => $nMinPre)
            ));

		//vai buscar o rating mais actual de cada jogador e cria uma array
		foreach ($playersList as $id => $name) {
			$rating = $this->get($id);

			$playerRatingList[] = array(
				'id' => $id,
				'name' => $name,
				'mean' => $rating['mean']);
		}

		//array_multisort
		//criar uma coluna que vai servir de referência, neste caso o rating (mean).
		foreach ($playerRatingList as $key => $row) {
		    $mean[$key] = $row['mean'];
		}
		array_multisort($mean, SORT_DESC, $playerRatingList);

		return $playerRatingList;

	}










}
	



























?>