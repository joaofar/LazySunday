<?php
/**
*	author: João Farinha 
*	date: 22 Nov 2013
*
*	É uma extensão sobre a aplicação PHPSkills (Moserware)
*	Eu fiz esta class 'TrueSkill' para poder ser usada no lazyfoot de forma prática.
*/

require_once('Skills/GameInfo.php');
require_once('Skills/Player.php');
require_once('Skills/Rating.php');
require_once('Skills/Team.php');
require_once('Skills/TrueSkill/TwoTeamTrueSkillCalculator.php');

use Moserware\Skills\GameInfo;
use Moserware\Skills\Player;
use Moserware\Skills\Rating;
use Moserware\Skills\Team;
use Moserware\Skills\Teams;
use Moserware\Skills\TrueSkill\TwoTeamTrueSkillCalculator;

class TrueSkill
{
	public $newRatings = array();

	public function __construct($teams)
	{
		/**
		 * Objecto onde se define os parametros do calculo do rating
		 * @param float $mean | rating médio da escala (0 a 10 seria 5)
		 * @param float $standardDeviation | grau de incerteza (por norma mean/3)
		 * @param float $beta | spread dos ratings (por norma mean/6)
		 * @param float $dynamicRange | dinamismo do rating (por norma mean/300)
		 * @param float $drawProbability | quando deve considerar empates
		 * @var GameInfo
		 */
		$gameInfo = new GameInfo(5, 5/3, 4.5, 0.008, 0);
		$calculator = new TwoTeamTrueSkillCalculator();
		
		//create team and player objects
		foreach ($teams as $key => $team) {
 			//create team
			${'team'.$key} = new Team();

			foreach ($team as $player) {
				//create player
				${'player'.$player['id']} = new Player($player['id']);
				//add player to team
				${'team'.$key}->addPlayer(
					${'player'.$player['id']},
					new Rating($player['mean'], $player['standard_deviation']))
				;
			}

			$teamsToRate[] = ${'team'.$key};
		}

		//calculate
		$ratings = $calculator->calculateNewRatings($gameInfo, $teamsToRate, array(1, 2));

		//set class variable
		foreach ($teams as $key => $team) {
			foreach ($team as $player) {
				$playerNewRating = $ratings->getRating(${'player'.$player['id']});
				$this->newRatings[] = array(
					'id' => $player['id'],
					'team_id' => $player['team_id'],
					'mean' => $playerNewRating->_mean,
					'standard_deviation' => $playerNewRating->_standardDeviation);
			}
		}
	}

	public function getRatings()
	{
		return $this->newRatings;
	}

	public function tralha_p_arrumar()
	{
		$player1 = new Player(1);
		$player2 = new Player(2);
		$player3 = new Player(3);
		$player4 = new Player(4);
		$player5 = new Player(5);

		$gameInfo = new GameInfo(5, 5/3, 5/6, 5/300, 0);

		$team1 = new Team();
		$team1->addPlayer($player1, $gameInfo->getDefaultRating());
		$team1->addPlayer($player2, $gameInfo->getDefaultRating());
		$team1->addPlayer($player3, $gameInfo->getDefaultRating());
		$team1->addPlayer($player4, $gameInfo->getDefaultRating());
		$team1->addPlayer($player5, $gameInfo->getDefaultRating());


		$player6 = new Player(6);
		$player7 = new Player(7);
		$player8 = new Player(8);
		$player9 = new Player(9);
		$player10 = new Player(10);

		$team2 = new Team();
		$team2->addPlayer($player6, $gameInfo->getDefaultRating());
		$team2->addPlayer($player7, $gameInfo->getDefaultRating());
		$team2->addPlayer($player8, $gameInfo->getDefaultRating());
		$team2->addPlayer($player9, $gameInfo->getDefaultRating());
		$team2->addPlayer($player10, $gameInfo->getDefaultRating());

		$teams = array($team1, $team2);

		$calculator = new TwoTeamTrueSkillCalculator();
		$newRatingsWinLose = $calculator->calculateNewRatings($gameInfo, $teams, array(1, 2));

		for($i=1; $i <= 10; $i++){
			echo 'player'.$i.' : '.$newRatingsWinLose->getRating(${'player'.$i}) . "</br>";
			if($i == 5){
				echo "</br>";
			}
		}
	}
	
}















