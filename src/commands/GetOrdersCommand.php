<?php

require_once __DIR__ . '/../core/Command.php';


class GetOrdersCommand extends Command
{

    private $message;


    public function __construct($id, $db, $message)
    {
        parent::__construct($id, $db);
    }

    public function start()
    {
                $html = '';
                if ($res = $this->db->query('SELECT * FROM orders WHERE making != 1')) {
                    $this->showKeyboard(TRUE, '<b>Все заказы:</b>', COMMANDSADM);
                    while ($slct = $res->fetch_assoc()) {
                        $html = $html . '--------' . PHP_EOL;
                        $html = $html . 'UserId - ' . $slct['userId'] . PHP_EOL;
                        $html = $html . 'Из адреса - ' . $slct['fromCity'] . PHP_EOL;
                        $html = $html . 'В адрес - ' . $slct['toCity'] . PHP_EOL;
                        $html = $html . 'Комментарий - ' . $slct['comment'] . PHP_EOL;
                        $html = $html . 'Время отправления - ' . $slct['timeOfDepart'] . PHP_EOL;
                        $html = $html . 'Цена - ' . $slct['cost'] . 'руб' . PHP_EOL;
                        $html = $html . '--------' . PHP_EOL;
                        $this->messageButtons('Заказ id '. $slct['id'] . PHP_EOL . $html,array(array('text' => 'Удалить', 'callback_data'=>'del')));
                        $html = '';
                    }
                return TRUE;
        }
    }
}
