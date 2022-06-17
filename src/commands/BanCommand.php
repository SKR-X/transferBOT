<?php

require_once __DIR__ . '/../core/Command.php';


class BanCommand extends Command
{

    private $select;
    private $message;


    public function __construct($id, $db, $message)
    {
        parent::__construct($id, $db);
        $this->message = $message;
    }

    public function start()
    {
        if (mb_strtolower($this->message['text']) == 'отмена') {
            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
            $this->showKeyboard(TRUE,'<b>Команда отменена. Вызвать заново - </b> "Забанить по userid"', COMMANDS);
            return TRUE;
        }
        $this->select = $this->select('users');
        switch ($this->select['step']) {
            case 'NONE':
                $this->db->query('UPDATE users SET step = \'ADMBAN\' WHERE userId = ' . $this->id);
                $this->sendHtml('<b>Введите userId:</b>');
                return TRUE;
            case 'ADMBAN':
                if (!empty($username = $this->db->query('SELECT * FROM users WHERE userId = \'' . $this->escape($this->message['text']).'\'')->fetch_assoc())) {
                    $this->db->query('INSERT INTO banned SET userId = ' . $this->escape($this->message['text']));
                    $this->sendHtml('<b>Юзер ' . $username['username'] . ' был забанен</b>');
                    $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                } else {
                    $this->sendHtml('<b>Юзера с таким ID нету в базе данных! Введите другой ID:</b>');
                }
                return TRUE;
        }
    }
}
