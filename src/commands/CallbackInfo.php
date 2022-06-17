<?php

require_once __DIR__ . '/../core/Command.php';

class CallbackInfo extends Command
{

    private $res;

    public function __construct($id, $res)
    {
        parent::__construct($id);
        $this->res = $res;
    }

    public function start()
    {
        if ($this->res) {
            $this->sendText('Заказ был удален!');
            return TRUE;
        } else {
            $this->sendText('Заказа с таким id не существует!');
            return TRUE;
        }
    }
}
