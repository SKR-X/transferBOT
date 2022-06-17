<?php

// require_once __DIR__ . '/../core/Command.php';


// class HelpCommand extends Command
// {

//     public function __construct($id)
//     {
//         parent::__construct($id);
//     }

//     public function start()
//     {
//         $html = '';
//         for ($i = 0; $i < count(COMMANDSHELP);$i++) {
//             if(!(COMMANDSHELP[$i][0]=='/register')) {
//             $html = $html . COMMANDSHELP[$i][0] . ' - ' . COMMANDSHELP[$i][1]['descr'] . PHP_EOL;
//             }
//         }

//         $text = '<b>Список команд:</b>' . PHP_EOL . $html;

//         $this->showKeyboard(TRUE,$text,COMMANDS);
//     }
// }
