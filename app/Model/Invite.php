<?php
App::uses('AppModel', 'Model');
/**
 * Invite Model
 *
 * @property Game $Game
 * @property Player $Player
 */
class Invite extends AppModel {
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
		'game_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
	);

 //The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 * 
 * @var array
 */
public $hasMany = array(
		'Rating' => array(
			'className' => 'Rating',
			'foreignKey' => '',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		));

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
		'Player' => array(
			'className' => 'Player',
			'foreignKey' => 'player_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * get method
 *
 * @param  int $id   game_id
 * @param  string $type ('invited', 'not_invited' e null devolve ambos)
 * @return array
 */
	public function get($id, $type = null)
	{	
		//find invites
		$invites = $this->find('list', array(
			'order' => array('Player.conv' => 'asc'),
	        'conditions' => array('game_id' => $id),
	        'fields' => array('player_id'),
	        'contain' => array('Player.fields' => array('Player.conv'))
	        ));

		//find invited Players, and invite status
		$invited = $this->Player->find('all', array(
			'conditions' => array('id' => $invites),
			'order' => array('conv' => 'ASC'),
			'contain' => array(
				'Rating.fields' => array('mean'), 
				'Rating.limit' => 1,
				'Invite.conditions' => array('game_id' => $id),
				'Invite.fields' => array('available','id'),
				'Invite.limit' => 1
				)));

		if ($type == 'invited') {
			$return[$type] = $invited;
		} elseif($type == 'not_invited' || $type == null) {
			//find not invited players
			$notInvited = $this->Player->find('all', array(
				'conditions' => array('id !=' => $invites),
				'contain' => array(
					'Rating.fields' => array('mean'), 
					'Rating.limit' => 1
					)));

			if ($type == 'not_invited') {
				$return[$type] = $notInvited;
			} else {
				$return = array(
					'invited' => $invited, 
					'not_invited' => $notInvited
					);
			}
		}

		//create array for view
		foreach ($return as $type => $players) {
		
			foreach ($players as $player) {

				if ($type == 'not_invited') {
					$player['Invite'][0] = array('id' => null, 'available' => null);
				}

				if (!isset($player['Rating'][0]['mean'])) {
					$player['Rating'][0]['mean'] = $this->Rating->get($player['Player']['id']);
				}

				$list[$type][] = array(
					'id' => $player['Player']['id'],
					'name' => $player['Player']['name'],
					'mean' => $player['Rating'][0]['mean'],
					'invite_id' => $player['Invite'][0]['id'],
					'available' => $player['Invite'][0]['available']
					);
			}
		}
		return $list;
	}



}
