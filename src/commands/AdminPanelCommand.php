<?php

require_once __DIR__ . '/../core/Command.php';


class AdminPanelCommand extends Command
{

    public function __construct($id, $db, $message)
    {
        parent::__construct($id, $db);
        $this->message = $message;
    }

    public function start()
    {
        $this->showKeyboard(TRUE,'<b>Открывем админ-панель...</b>',COMMANDSADM);
    }
}
