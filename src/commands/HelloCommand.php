<?php

require_once __DIR__ . '/../core/Command.php';


class HelloCommand extends Command
{

    private $message;
    private $select;

    public function __construct($id, $db, $message)
    {
        parent::__construct($id, $db);
        $this->message = $message;
        $this->select = $this->select('users');
    }

    public function start()
    {
        if (!isset($this->message['from']['username'])) {
            $this->sendHtml('<b>Вы обязаны иметь имя пользователя в настройках профиля Telegram для использования бота!</b>');
            return TRUE;
        } elseif (empty($this->select)) {
            $this->db->query('INSERT INTO users (username,userId,step,grade) VALUES (\'' . $this->message['from']['username'] . '\',' . $this->id . ',\'NONE\',\'User\')');
            $this->showKeyboard(TRUE,'<b>Здравствуйте!</b> Регистрация - /register', array('/register'));
            return TRUE;
        }
    }
}
