<?php

require_once __DIR__ . '/../core/Command.php';


class SearchOrderCommand extends Command
{

    private $message;
    private $selectUsers;
    private $selectOrders;

    public function __construct($id, $db, $message = NULL)
    {
        parent::__construct($id, $db);
        $this->message = $message;
        $this->selectUsers = $this->select('users');
        $this->selectOrders = $this->select('orders');
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
        $i = 1;
        if (mb_strtolower($this->message['text']) == 'отмена' && $this->selectUsers['step']=='NONE') {
            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
            $this->showKeyboard(TRUE,'<b>Команда отменена. Вызвать заново - </b> "Поиск"', COMMANDS);
            return TRUE;
        } elseif(mb_strtolower($this->message['text']) == 'отмена' && $this->selectUsers['step']!='NONE') {
            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
            $this->db->query('DELETE FROM searching WHERE userId = ' . $this->id);
            $this->showKeyboard(TRUE,'<b>Команда отменена. Вызвать заново - </b> "Поиск"', COMMANDS);
            return TRUE;
        }

        switch ($this->selectUsers['step']) {
            case 'NONE':
                $this->db->query('DELETE FROM orders WHERE timeOfDepart < CURDATE()');
                if (empty($from = $this->db->query('SELECT * FROM orders ORDER BY id ASC')->fetch_assoc())) {
                    $this->showKeyboard(TRUE,'<b>Еще не было создано ни одного заказа!</b>', COMMANDS);
                    return TRUE;
                }
                $this->db->query('DELETE FROM orders WHERE timeOfDepart < CURDATE()');
                $this->db->query('UPDATE users SET step = \'SEARCHORDER1\' WHERE userId = ' . $this->id);
                $html = '';
                if (($res = $this->db->query('SELECT * FROM orders GROUP BY fromCity')) != FALSE) {
                    while ($from = $res->fetch_assoc()) {
                        $html = $html . $i++ . ' - ' . $from['fromCity'] . PHP_EOL;
                    }
                }
                $this->showKeyboard(TRUE,'<b>Выберите адрес отправления, доступные адреса:</b>' . PHP_EOL . $html, array('Отмена'));
                return TRUE;
            case 'SEARCHORDER1':
                if (!isset($this->message['text'])) {
                    $this->sendHtml('<b>Выберите адрес отправления</b>');
                    return TRUE;
                }
                    $this->db->query('INSERT INTO searching SET sql1 = \'' . $this->escape($this->message['text']) . '\', userId = ' . $this->id);
                if (($res = $this->db->query('SELECT * FROM orders WHERE fromCity = (SELECT sql1 FROM searching WHERE userId = \'' . $this->id . '\') GROUP BY id')) != FALSE) {
                    while ($from = $res->fetch_assoc()) {
                        $html = $html . $i++ . ' - ' . $from['toCity'] . PHP_EOL;
                    }
                }
                if($html!='') {
                $this->db->query('UPDATE users SET step = \'SEARCHORDER2\' WHERE userId = ' . $this->id);
                $this->showKeyboard(TRUE,'<b>Выберите адрес прибытия, доступные адреса:</b>' . PHP_EOL . $html, array('Отмена'));
                } else {
                    $this->sendHtml('<b>Такого адреса нет в списках, выберите другой</b>');
                }
                return TRUE;
            case 'SEARCHORDER2':
                if (!isset($this->message['text'])) {
                    $this->sendHtml('<b>Выберите адрес прибытия:</b>');
                    return TRUE;
                }
                    $this->db->query('UPDATE searching SET sql2 = \'' . $this->escape($this->message['text']) . '\', userId = ' . $this->id);
                if (($res = $this->db->query('SELECT * FROM orders WHERE toCity = (SELECT sql2 FROM searching WHERE userId = \'' . $this->id . '\') AND fromCity = (SELECT sql1 FROM searching WHERE userId = \'' . $this->id . '\') GROUP BY id')) != FALSE) {
                $this->db->query('UPDATE users SET step = \'SEARCHORDER3\' WHERE userId = ' . $this->id);
                $this->sendHtml('<b>От даты:</b>');
                }
                return TRUE;
            case 'SEARCHORDER3':
                if (!isset($this->message['text']) || !$this->isdate($this->message['text'])) {
                    $this->sendHtml('<b>От даты:</b>');
                    return TRUE;
                }
                $select = $this->db->query('SELECT * FROM orders WHERE fromCity = (SELECT sql1 FROM searching WHERE userId = \'' . $this->id . '\') AND toCity = (SELECT sql2 FROM searching WHERE userId = \'' . $this->id . '\') AND timeOfDepart >= \''.$this->escape($this->message['text']).'\'')->fetch_assoc();
                if(!empty($select)) {
                    $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                $this->db->query('DELETE FROM searching WHERE userId = ' . $this->id);
                $this->showKeyboard(TRUE,$select['link'],COMMANDS);
                } else {
                    $this->sendHtml('<b>Заказов от выбранной даты нет, введите другую дату:</b>');
                }
                return TRUE;
        }

    }
}
