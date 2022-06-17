<?php

require_once __DIR__.'/../core/Command.php';


class ErrorCommand extends Command
{   

    private $case;

    public function __construct($id,$case) 
    {
        parent::__construct($id,$case);
        $this->case = $case;
    }

    public function start()
    {
        if($this->case == 1) {
        $this->sendText('У вас недостаточно прав на выполнение этой команды!');
        } else if($this->case == 2) {
            $this->sendText('Вы были забанены!');
        }
        return TRUE;
    }
}
