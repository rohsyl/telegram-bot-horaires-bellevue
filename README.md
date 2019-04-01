# Telegram Bot - Horaires Bellevue

This is a telegram bot written in PHP. It give you course scheduling for the current day.

## Preparation

### Have a webserver with HTTPS

Exemple: https://telegram.rohs.ch

### Clone this repository on your server

```
git clone git@github.com:rohsyl/telegram-bot-horaires-bellevue.git
```

## Installing the bot

### Create a bot

On the Telegram app create a new bot by talking to the [BotFather](https://telegram.me/botfather).

More details on How to create a bot [here](https://core.telegram.org/bots#6-botfather).

### Get the API token

Talk to the [BotFather](https://telegram.me/botfather) and type:

```
/mybots
```
  
Then click on your bot and next click on "API Token" to get your token

## Configure the bot and register the hook

### Configure

Edit the file `constant.php` and set `APIKEY` and `BOTNAME`

Edit the file `set.php` and change the `$hook_url` to point to your hook.php file on your own domain

Install dependencies

```
composer install
```

### Set the hook

Open https://telegram.rohs.ch/set.php in your browser.

You will get a success message if the hook is successfully registred.

You can now write to the bot on the Telegram app
