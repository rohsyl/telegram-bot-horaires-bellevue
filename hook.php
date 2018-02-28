<?php
// Load composer
require __DIR__ . '/constant.php';
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/library/HES_Schedule.php';
require __DIR__ . '/library/GroupDataReader.php';


// https://github.com/php-telegram-bot/core
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Commands\SystemCommands\CallbackqueryCommand;
use Longman\TelegramBot\Commands\UserCommands\ConfigureCommand;

try {
    // Create Telegram API object
    $telegram = new Telegram(APIKEY, BOTNAME);
	
    $telegram->addCommandsPath(CMD_DIRECTORY);
	
	$telegram->enableAdmin(BOT_ADMIN);
	
	CallbackqueryCommand::addCallbackHandler(function($callbackquery){
		ConfigureCommand::SetClassNo($callbackquery);
	});
	
    $telegram->handle();
} 
catch (TelegramException $e) {
    // Silence is golden!
    // log telegram errors
    // echo $e;
}
catch (Exception $e ){
    file_put_contents('errors.log', print_r($e, true), FILE_APPEND);
}