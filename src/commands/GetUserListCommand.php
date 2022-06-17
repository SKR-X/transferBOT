<?php

require_once __DIR__ . '/../core/Command.php';


class GetUserListCommand extends Command
{

    private $message;

    public function __construct($id, $db,$message)
    {
        parent::__construct($id, $db);
        $this->message = $message;
    }

    public function start()
    {
        $html = '';
        if ($res = $this->db->query('SELECT * FROM users')) {
            $this->showKeyboard(TRUE,'<b>Все юзеры:</b>', COMMANDSADM);
            while ($slct = $res->fetch_assoc()) {
                $this->sendHtml('<b>userid - </b>' . $slct['userId'] . PHP_EOL . 'Ссылка - ' . $slct['username']);
                $html = '';
            }
            return TRUE;
        }
    }
}
