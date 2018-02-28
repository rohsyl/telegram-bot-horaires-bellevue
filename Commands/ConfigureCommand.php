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
use Longman\TelegramBot\Entities\InlineKeyboardButton;

use \HES_Schedule;
use \GroupDataReader;

/**
 * User "/wai" command
 */
class ConfigureCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'configure';
    protected $description = 'Configurer quelle classe doit utiliser le bot pour donner les horaires';
    protected $usage = '/configure';
    protected $version = '0.1.0';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
		$message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
		$arg  = trim($message->getText(true));
		try {
			if(!$message->getChat()->isPrivateChat()){
				$adminIds = self::getChatAdminsId($chat_id);
				
				if(!in_array($message->getFrom()->getId(), $adminIds)){
					$data = array(
						'chat_id'      => $chat_id,
						'text'         => 'Only admin can run /configure'
					);
					return Request::sendMessage($data);
				}
			}
			
			if($arg == 'help'){
				$data = array(
					'chat_id'      => $chat_id,
					'text'         => $this->getUsage()
				);
				return Request::sendMessage($data);
			}
			
			$classes = HES_Schedule::getClassList();
			
			$inline_keyboard = array();
			foreach($classes as $class){
				$inline_keyboard[] = new InlineKeyboardButton([
					'text' => $class['name'], 
					'callback_data' => 'classno_'.$class['id']
				]);
			}
			$inline_keyboard = array_chunk($inline_keyboard, 3);
		
			/*
				[
					[['text' =>  $name1, 'callback_data' => $placeId1]], 
					[['text' =>  $name2, 'callback_data' => $placeId2]]
				]
			*/
		
			$data = [
				'chat_id' => $chat_id,
				'text'    => 'Choisissez une classe :',
				'reply_markup' => ['inline_keyboard' => $inline_keyboard],
			];

			return Request::sendMessage($data);
		}
		catch (Exception $e ){
			$data = [
				'chat_id'      => $chat_id,
				'text'         => print_r($e, true)
			];

			return Request::sendMessage($data);
		}
    }
	
	private static function getChatAdminsId($chat_id){
		$chat_members = Request::getChatAdministrators(array('chat_id' => $chat_id));
			
		$adminIds = array();
		foreach($chat_members->result as $chat_member){
			$adminIds[] = $chat_member->getUser()->getId();
		}
		return $adminIds;
	}

	
	public static function SetClassNo($callback_query){
		
		$callback_data  = $callback_query->getData();
		$message = $callback_query->getMessage();
        $chat_id = $message->getChat()->getId();
		
		
		if(!$message->getChat()->isPrivateChat()){
			$adminIds = self::getChatAdminsId($chat_id);
			
			if(!in_array($callback_query->getFrom()->getId(), $adminIds)){
					
				$username = $callback_query->getFrom()->getUsername();
				if(empty($username))
					$username = $callback_query->getFrom()->getFirstName() . ' ' . $callback_query->getFrom()->getLastName();
				else
					$username = '@'.$username;
				$data = array(
					'chat_id'      => $chat_id,
					'text'         => 'Sorry '.$username.'. Only admin can click on this button. lol noob'
				);
				return Request::sendMessage($data);
			}
		}
		
		if (strpos($callback_data, 'classno_') !== 0) {
            return Request::emptyResponse();
        }
		
        $classno = substr($callback_data, strlen('classno_'));

		GroupDataReader::setClassIdByGroup($chat_id, $classno);
		
		$data = array(
			'chat_id'      => $chat_id,
			'text'         => 'Classe définie !'
		);
		return Request::sendMessage($data);
	}
}
