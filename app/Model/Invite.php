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
 * @param  string $type ('invited' or 'not_invited')
 * @param  int $id   game_id
 * @return array
 */
public function get($type, $id)
{	
	//find invites
	$invites = $this->find('list', array(
		'order' => array('Player.conv' => 'asc'),
        'conditions' => array('game_id' => $id),
        'fields' => array('player_id'),
        'contain' => array('Player.fields' => array('Player.conv'))
        ));

	switch ($type) {
		case 'invited';
			//find invited Players
			$invited = $this->Player->find('all', array(
				'conditions' => array('id' => $invites),
				'contain' => array(
					'Rating.fields' => array('mean'), 
					'Rating.limit' => 1)
				));

			//create array for view
			foreach ($invited as $player) {
				$list['invited'][] = array(
					'id' => $player['Player']['id'],
					'name' => $player['Player']['name'],
					'mean' => $player['Rating'][0]['mean']
					);
			}

			return $list;
			break;

		case 'not_invited':
			//find not invited players
			$notInvited = $this->Player->find('all', array(
				'conditions' => array('id !=' => $invites),
				'contain' => array(
					'Rating.fields' => array('mean'), 
					'Rating.limit' => 1)
				));

			//create array for view
			foreach ($notInvited as $player) {
				$list['not_invited'][] = array(
					'id' => $player['Player']['id'],
					'name' => $player['Player']['name'],
					'mean' => $player['Rating'][0]['mean']
					);
			}

			return $list;
			break;

		
	}

	// foreach ( as $player) {
	// 	$invited[] = $player['Player'];
	// };

	// return $this->Player->find('all', array(
	// 	'conditions' => array('game_id' => $gameId)
	// 	));
}

/**
 * invites method
 *
 * @param int $id gameId
 * @return array
 */
    public function invites($id) {
        $invites = $this->find('all', array(
        	'order' => array('Player.conv' => 'asc', 'Player.rating' => 'desc'), 
        	'conditions' => array('game_id' => $id), 
        	'recursive' => 1));
        $players = $this->Player->find('list');

        foreach($invites as $invite) {
            $invite_list[$invite['Invite']['player_id']] = null;
        }
        foreach($players as $key => $player) {
            if(!array_key_exists($key, $invite_list)) {
                $notinvited[$key] = $player;
            }
        }

        return array('invites' => $invites,
                     'notinvited' => $notinvited);

    }
}
