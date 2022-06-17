<?php

require_once __DIR__ . '/../core/Command.php';


class MainMenuCommand extends Command
{

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function start()
    {
        $this->showKeyboard(TRUE,'Открываем главное меню...',(array) COMMANDS);
    }
}
