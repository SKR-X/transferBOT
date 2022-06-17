<?php

require_once __DIR__ . '/../core/Command.php';


class CreateOrderWithoutMenuCommand extends Command
{

    private $exp;
    private $message;

    public function __construct($id,$db,$message,$exp)
    {
        parent::__construct($id,$db);
        $this->exp = $exp;
        $this->message = $message;
    }

    private function isDate($str)
    {
        if (preg_match("/^([1-2][0-9]{3})-(0[1-9]|1[0-2])-([0-2][1-9]|3[0-1]) ([0-1][0-9]|2[0-3]):([0-5][0-9])$/", $str)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function start()
    {
        for ($i = 0; $i < count($this->exp); $i++) {
            $this->exp[$i] = $this->escape($this->exp[$i]);
        }
        if (!$this->isDate($this->exp[0])) { 
            $this->sendHtml('<b>Некорректное значение даты!</b>');
        } else {
            $this->db->query('INSERT INTO orders (timeOfDepart,fromCity,toCity,autoClass,comment,userId) VALUES (\'' . $this->exp[0] . '\',\'' . explode('-', $this->exp[1])[0] . '\',\'' . explode('-', $this->exp[1])[1] . '\',\'' . $this->exp[2] . '\',\'' . $this->exp[3] . '\',' . $this->id . ')');
            $this->sendHtml('<b>Успешное создание заказа!</b>');
        }
    }
}
