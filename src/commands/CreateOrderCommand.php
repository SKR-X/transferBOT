<?php

require_once __DIR__ . '/../core/Command.php';


class CreateOrderCommand extends Command
{

    private $message;
    private $select;

    public function __construct($id, $db, $message = NULL)
    {
        parent::__construct($id, $db);
        $this->message = $message;
        $this->select = $this->select('users');
    }

    private function isDate($str)
    {
        if(is_numeric(strtotime($str)) && count(explode(' ',$str)) == 2) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function start()
    {
        if (mb_strtolower($this->message['text']) == 'отмена' && $this->select['step'] == 'CREATEORDER1') {
            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
            $this->showKeyboard(TRUE, '<b>Команда отменена. Вызвать заново - </b> "Создать заказ"', COMMANDS);
            return TRUE;
        } else if (mb_strtolower($this->message['text']) == 'отмена' && $this->select['step'] != 'NONE') {
            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
            $this->db->query('DELETE FROM orders WHERE userId = ' . $this->id . ' AND making = 1 ORDER BY id DESC LIMIT 1');
            $this->showKeyboard(TRUE, '<b>Команда отменена. Вызвать заново - </b> "Создать заказ"', COMMANDS);
            return TRUE;
        }
        switch ($this->select['step']) {
            case 'NONE':
                $this->db->query('UPDATE users SET step = \'CREATEORDER1\' WHERE userId = ' . $this->id);
                $this->showKeyboard(TRUE, '<b>Адрес отправления:</b>', array('Отмена'));
                return TRUE;
            case 'CREATEORDER1':
                if (!isset($this->message['text'])) {
                    $this->sendHtml('<b>Адрес отправления:</b>');
                    return TRUE;
                } else {
                    $adr1 = $this->message['text'];
                    $this->db->query('INSERT INTO orders SET fromCity = \'' . $this->escape($this->message['text']) . '\', making = 1, userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'CREATEORDER2\' WHERE userId = ' . $this->id);
                    $this->sendHtml('<b>Адрес прибытия:</b>');
                    return TRUE;
                }
            case 'CREATEORDER2':
                if (!isset($this->message['text'])) {
                    $this->sendHtml('<b>Адрес прибытия:</b>');
                    return TRUE;
                } else {
                    $adr2 = $this->message['text'];
                    $this->db->query('UPDATE orders SET toCity = \'' . $this->escape($this->message['text']) . '\' WHERE making = 1 AND userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'CREATEORDER3\' WHERE userId = ' . $this->id);
                    $this->sendHtml('<b>Время отправления:</b>');
                    return TRUE;
                }
            case 'CREATEORDER3':
                if (!isset($this->message['text']) || !$this->isDate($this->message['text'])) {
                    $this->sendHtml('<b>Время отправления:</b>');
                    return TRUE;
                } else {
                    $time = $this->message['text'];
                    $this->db->query('UPDATE orders SET timeOfDepart = \'' . $this->escape($this->message['text']) . '\' WHERE making = 1 AND userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'CREATEORDER4\' WHERE userId = ' . $this->id);
                    $this->sendHtml('<b>Минимальный класс автомобиля (введите цифру):</b>' . PHP_EOL . '1 - Эконом' . PHP_EOL . '2 - Комфорт' . PHP_EOL . '3 - Комфорт +' . PHP_EOL . '4 - Минивен (микроавтобус)' . PHP_EOL . '5 - Бизнес');
                    return TRUE;
                }
            case 'CREATEORDER4':
                if (!isset($this->message['text'])) {
                    $this->sendHtml('<b>Минимальный класс автомобиля (введите цифру):</b>');
                    return TRUE;
                } else {
                    switch ($this->message['text']) {
                        case 1:
                            $class = 'Эконом';
                            $this->db->query('UPDATE orders SET autoClass = \'Эконом\' WHERE making = 1 AND userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'CREATEORDER5\' WHERE userId = ' . $this->id);
                            $this->sendHtml('<b>Описание:</b>');
                            return TRUE;
                        case 2:
                            $class = 'Комфорт';
                            $this->db->query('UPDATE orders SET autoClass = \'Комфорт\' WHERE making = 1 AND userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'CREATEORDER5\' WHERE userId = ' . $this->id);
                            $this->sendHtml('<b>Описание:</b>');
                            return TRUE;
                        case 3:
                            $class = 'Комфорт +';
                            $this->db->query('UPDATE orders SET autoClass = \'Комфорт +\' WHERE making = 1 AND userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'CREATEORDER5\' WHERE userId = ' . $this->id);
                            $this->sendHtml('<b>Описание:</b>');
                            return TRUE;
                        case 4:
                            $class = 'Минивен (микроавтобус)';
                            $this->db->query('UPDATE orders SET autoClass = \'Минивен (микроавтобус)\' WHERE making = 1 AND userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'CREATEORDER5\' WHERE userId = ' . $this->id);
                            $this->sendHtml('<b>Описание:</b>');
                            return TRUE;
                        case 5:
                            $class = 'Бизнес';
                            $this->db->query('UPDATE orders SET autoClass = \'Бизнес\' WHERE making = 1 AND userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'CREATEORDER5\' WHERE userId = ' . $this->id);
                            $this->sendHtml('<b>Описание:</b>');
                            return TRUE;
                        default:
                            $this->sendHtml('<b>Минимальный класс автомобиля (введите цифру):</b>');
                            return TRUE;
                    }
                }
            case 'CREATEORDER5':
                if (!isset($this->message['text'])) {
                    $this->sendHtml('<b>Описание:</b>');
                    return TRUE;
                } else {
                    $select = $this->db->query('SELECT * FROM orders WHERE userId = ' . $this->id . ' AND making = 1 ORDER BY id DESC LIMIT 1')->fetch_assoc();
                    $curl = curl_init();
                    $data = array(
                        'from' => $select['fromCity'],
                        'to' => $select['toCity'],
                        'id' => $this->id
                    );
                    curl_setopt($curl, CURLOPT_URL, 'https://swsh.in/bot-php/src/web/script.php');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

                    $out = curl_exec($curl);

                    fwrite(fopen('ww.txt','w'),htmlspecialchars($out));

                    curl_close($curl);
                    $select = $this->db->query('SELECT * FROM orders WHERE userId = ' . $this->id . ' AND making = 1 ORDER BY id DESC LIMIT 1')->fetch_assoc();
                    $desc = $this->escape($this->message['text']);
                    $this->db->query('UPDATE orders SET comment = \'' . $desc . '\' WHERE making = 1 AND userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'CREATEORDER6\' WHERE userId = ' . $this->id);
                    $this->sendHtml('<b>Цена (введите цифру, минимал - '.$select['cost'].'):</b>');
                    return TRUE;
                }
            case 'CREATEORDER6':
                if (!isset($this->message['text']) || $this->message['text'] < 1500) {
                    $this->sendHtml('<b>Цена (введите цифру, минимум 1500руб):</b>');
                    return TRUE;
                } else {
                    $link = $this->select('users')['username'];
                    $this->db->query('UPDATE orders SET cost = \'' . $this->escape($this->message['text']) . '\', link = \'' . $link . '\' WHERE making = 1 AND userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                    $this->showKeyboard(TRUE, '<b>Успешное создание заказа!</b>', COMMANDS);
                    $lastId = $this->id;
                    if (($res = $this->db->query('SELECT toDate,fromDate,userId FROM users WHERE userId != '.$lastId)) != FALSE) {
                        while ($select = $res->fetch_assoc()) {
                            if ($this->db->query('SELECT timeOfDepart FROM orders WHERE making = 1 AND userId = ' . $this->id)) {
                                $timeOfDep = explode(' ', $this->db->query('SELECT timeOfDepart FROM orders WHERE making = 1 AND userId = ' . $this->id)->fetch_assoc()['timeOfDepart'])[0];
                                if ($timeOfDep >= $select['fromDate'] && $timeOfDep <= $select['toDate']) {
                                    $arr = $this->db->query('SELECT * FROM orders WHERE userId = ' . $lastId. ' AND making = 1 ORDER BY id DESC LIMIT 1')->fetch_assoc();
                                    $this->id = $select['userId'];
                                    $this->sendHtml('Новый заказ!' . PHP_EOL . 'Сслыка на юзера - ' . $link . PHP_EOL . 'Адрес отправления - ' .
                                        $arr['fromCity'] . PHP_EOL . 'Адрес прибытия - ' . $arr['toCity'] . PHP_EOL . 'Время отправления - ' . $arr['timeOfDepart'] . PHP_EOL . 'Минимальный класс авто - ' . $arr['autoClass'] . PHP_EOL . 'Описание - ' . $arr['comment'] . PHP_EOL . 'Цена - ' . $arr['cost'] . 'руб');
                                }
                            }
                        }
                        $this->db->query('UPDATE orders SET making = 0 WHERE making = 1 AND userId = ' . $lastId);
                        return TRUE;
                    }
                }
        }
    }
}
