<?php

require_once __DIR__ . '/../core/Command.php';


class DateCommand extends Command
{

    private $message;
    private $select;

    public function __construct($id, $db, $message = NULL)
    {
        parent::__construct($id, $db);
        $this->message = $message;
        $this->select = $this->select('users');
    }

    public function isDate($str)
    {
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function start()
    {
        if (mb_strtolower($this->message['text']) == 'отмена') {
            $this->db->query('UPDATE users SET step = \'NONE\',FIO = \'\',drivePhotoId = \'\',autoPhotoId = \'\',autoClass = \'\' WHERE userId = ' . $this->id);
            $this->showKeyboard(TRUE,'<b>Команда отменена. Вызвать заново - </b> "Поставить диапазон дат"', (array) COMMANDSSETTING);
            return TRUE;
        }
        $this->select = $this->select('users');
        switch ($this->select['step']) {
            case 'NONE':
                $this->db->query('UPDATE users SET step = \'DATE1\' WHERE userId = ' . $this->id);
                $this->showKeyboard(TRUE,'<b>Получать заказы от даты:</b> (ввод в виде ГГГГ-ММ-ДД)', array('Отмена'));
                return TRUE;
            case 'DATE1':
                if (!isset($this->message['text']) || !$this->isDate($this->message['text'])) {
                    $this->sendHtml('<b>Получать заказы от даты:</b> (ввод в виде ГГГГ-ММ-ДД)');
                    return TRUE;
                } else {
                    $this->db->query('UPDATE users SET fromDate = \'' . $this->escape($this->message['text']) . '\' WHERE userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'DATE2\' WHERE userId = ' . $this->id);
                    $this->sendHtml('<b>До даты:</b> (ввод в виде ГГГГ-ММ-ДД)');
                    return TRUE;
                }
            case 'DATE2':
                if (!isset($this->message['text']) || !$this->isDate($this->message['text'])) {
                    $this->sendHtml('<b>До даты:</b> (ввод в виде ГГГГ-ММ-ДД)');
                    return TRUE;
                } else {
                    $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                    $this->db->query('UPDATE users SET toDate = \'' . $this->escape($this->message['text']) . '\' WHERE userId = ' . $this->id);
                    $this->showKeyboard(TRUE,'<b>Даты были успешно настроены!</b>',COMMANDS);
                    return TRUE;
                }
        }
    }
}
