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
 * User "/about" command
 */
class AboutCommand extends UserCommand
{
    /**#@+
     * {@inheritdoc}
     */
    protected $name = 'about';
    protected $description = 'Information à propos du bot';
    protected $usage = '/about';
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
            'text'         => 'Par Sylvain Roh'
        ];
        return Request::sendMessage($data);
    }
}
