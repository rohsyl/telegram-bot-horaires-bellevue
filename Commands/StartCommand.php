<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;


/**
 * User "/start" command
 */
class StartCommand extends SystemCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'start';
    protected $description = 'Lancer le bot';
    protected $usage = '/start';
    protected $version = '0.1.0';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
		$message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
	
		$data = [
			'chat_id'      => $chat_id,
			'text'         => 'Bienvenue sur syzinBot!' . PHP_EOL .
							  'Vous pouvez utiliser la commande "/configure" pour lancer le paramétrage du bot.' . PHP_EOL .
							  'Une fois configuré, utiliser la commande "/wai" pour afficher les horaires de cours.' . PHP_EOL .
							  'Utilisez "/wai help" pour les details de la commande wai.'
		];

		return Request::sendMessage($data);
    }
	
}
