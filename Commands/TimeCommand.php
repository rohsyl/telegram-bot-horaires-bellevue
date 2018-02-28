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
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;


/**
 * User "/uni" command
 */
class TimeCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'time';
    protected $description = 'Donne l\'heure, inutile quoi!';
    protected $usage = '/time';
    protected $version = '0.1.0';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
		$message = $this->getMessage();
        $chat_id = $message->getChat()->getId();
		date_default_timezone_set("Europe/Zurich");
        $data = [
            'chat_id'      => $chat_id,
            'text'         => 'Il est '. date('H:i:s')
        ];
        return Request::sendMessage($data);
    }
}
