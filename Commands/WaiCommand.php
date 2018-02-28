<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;

use \HES_Schedule;
use \GroupDataReader;

/**
 * User "/wai" command
 */
class WaiCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'wai';
    protected $description = 'Dans quel salle on est ajd putin ?';
    protected $usage = '/wai [-a]|[+x]';
    protected $version = '0.2.0';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
		$message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
		$arg  = trim($message->getText(true));
		
		if($arg == 'help'){
			$data = array(
				'chat_id'      => $chat_id,
				'text'         => 'Aide :' . PHP_EOL .
								  '/wai 		=> Affiche les horaires du jour.' . PHP_EOL .
								  '/wai -a 		=> Affiche les horaires de la semaine.' . PHP_EOL .
								  '/wai +x		=> Affiche les horaires dans x jour(s)'
			);
			return Request::sendMessage($data);
		}
		
		// handle args
		$currentDayOnly = true;
		$padDay = 0;
		if($arg != ''){
			// arg : -a -> get all week schedule
			if($arg == '-a'){
				$currentDayOnly = false;
			}
			// arg : +1 -> get next day schedule
			else if(substr($arg, 0, 1) == '+') {
				if(strlen($arg) > 1){
					$pd = substr($arg, 1);
					if($pd < 10){
						$padDay = $pd;
					}
				}
			}
			// if argument dont exists
			else{
				$data = array(
					'chat_id' => $chat_id,
					'text'    => 'Unknow argument ' . $arg . '. Please use "/wai help" to learn how to use me. noob'
				);
				return Request::sendMessage($data);
			}
		}
		
		$noClasse = GroupDataReader::getClassIdByGroup($chat_id);
		
		// if no noclasse defined for this group. display warning message
		if($noClasse === false) {
			$data = array(
				'chat_id' => $chat_id,
				'text'    => 'Aucune classe definie, un admin doit executer /configure'
			);
			return Request::sendMessage($data);
		}
		// get schedule of the day
		if($currentDayOnly){
			
			$dateToday = date(OUT_DATE_FORMAT);
			
			if($padDay > 0){
				$dateToday = date(OUT_DATE_FORMAT, strtotime('now + '.$padDay.' days'));
			}
			$dayofweek = $this->getDayOfWeekLiteral(date('w', strtotime($dateToday)));

			$schedule = HES_Schedule::getSchedule($noClasse);
			
			// if today's date exists -> display schedule
			if(array_key_exists($dateToday, $schedule)){
				$today = $schedule[$dateToday];
				
				$out = " 📅   ". $dayofweek . " " . $dateToday  . PHP_EOL . PHP_EOL;
				foreach($today as $s){
					$out .= " 🎓 ".$s['module'].PHP_EOL;
					$out .= " 🕑 ".$s['time-start']." - ".$s['time-end'].PHP_EOL;
					$out .= " 🏢 ".$s['room'].PHP_EOL;
					$out .= " 👤 ".$s['prof'].PHP_EOL;
					$out .= PHP_EOL;
				}

				$data = [
					'chat_id'      => $chat_id,
					'text'         => $out
				];
				return Request::sendMessage($data);
			}
			// else there is nothing today
			else{
				$out = " 📅   ".$dateToday . PHP_EOL . 'Pas de cours';
				$data = [
					'chat_id' => $chat_id,
					'text'    => $out
				];
				return Request::sendMessage($data);
			}
		}
		else{
			
			$schedule = HES_Schedule::getSchedule($noClasse, false);
			$schedule = $schedule[0];
			$out = 'Semaine du ' . $schedule['date'] .  PHP_EOL . PHP_EOL;
			foreach($schedule['schedule'] as $dateToday => $today){
				$out .= " 📅   ".$dateToday . PHP_EOL ;
				foreach($today as $s){
					$out .= " 🕑 ".$s['time-start']." - ".$s['time-end'];
					$out .= " - ".$s['module'].PHP_EOL;
				}
				$out .= PHP_EOL;
			}
			
			$data = [
				'chat_id' => $chat_id,
				'text'    => $out
			];

			return Request::sendMessage($data);
		}
    }
	
	
	private function getDayOfWeekLiteral($w){
		switch($w){
			case 2:
				return 'Mardi';
			case 3:
				return 'Mercredi';
			case 4:
				return 'Jeudi';
			case 5:
				return 'Vendredi';
			default:
				return 'Lundi';
		}
    }
}
