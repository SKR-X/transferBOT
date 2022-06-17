<?php

require_once __DIR__ . '/../core/Command.php';


class SettingCommand extends Command
{

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function start()
    {
        echo "var request = new XMLHttpRequest();
        request.open('GET', 'https://swsh.in/bot-php/src/web/Hook.php', true);
        
        request.onload = function() {
          if (request.status >= 200 && request.status < 400) {
            var data = JSON.parse(request.responseText);
            console.log(data);
          } else {
            // error
          }
        };
        
        request.send();";
        $this->showKeyboard(TRUE,'<b>Открываем настройки...</b>',(array)COMMANDSSETTING);
    }

}