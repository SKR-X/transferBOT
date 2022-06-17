<?php

// echo "<script>window.location.replace('http://ya.ru');</script>";

$db = new mysqli('localhost', 'g53334_dbuser', '1c4jB!?CP^bw>!RL', 'g53334_db');

$body = file_get_contents('php://input');

$arr = json_decode($body, true);

$userId = $arr['message']['from']['id'];

$message = $arr['message'];

$text = mb_strtolower($message['text']);

if(isset($arr['callback_query']['message']['text'])) {

$id = explode('--------',$arr['callback_query']['message']['text'])[0];

$id = explode(' ',$id)[2];

if($db->query('SELECT * FROM orders WHERE id = '.$id) && !empty($db->query('SELECT * FROM orders WHERE id = '.$id)->fetch_assoc())) {
    $db->query('DELETE FROM orders WHERE id = '.$id);
    require_once __DIR__ . '/../commands/CallbackInfo.php';
    $obj = new CallbackInfo($arr['callback_query']['message']['chat']['id'],TRUE);
    $obj->start();
    exit('ok');
} else {
    require_once __DIR__ . '/../commands/CallbackInfo.php';
    $obj = new CallbackInfo($arr['callback_query']['message']['chat']['id'],FALSE);
    $obj->start();
    exit('ok');
}

}

if($select = $db->query('SELECT * FROM users WHERE userId = '.$userId)) {
    $select = $select->fetch_assoc();
}

if($db->query('SELECT * FROM banned WHERE userId = '.$userId) && !empty($db->query('SELECT * FROM banned WHERE userId = '.$userId)->fetch_assoc())) {
    require_once __DIR__ . '/../commands/ErrorCommand.php';
    $obj = new ErrorCommand($userId,2);
    $obj->start();
    exit('ok');
}

mb_internal_encoding("UTF-8");
function mb_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}

$commands = require_once __DIR__ . '/../Commands.php';

$i = 0;

foreach ($commands as $key => $value) {
    if ($value['descr'] == 'Setting') {
        $definedset[$i] = mb_ucfirst($key);
        $i++;
    }
}

$i = 0;

if($db->query('SELECT * FROM users WHERE grade = \'ADM\' AND userId = '.$userId) && $db->query('SELECT * FROM users WHERE grade = \'ADM\' AND userId = '.$userId)->fetch_array()) {
foreach ($commands as $key => $value) {
    if ($value['descr'] == 'Main' || $value['descr'] == 'ADM1') {
        $defined[$i] = mb_ucfirst($key);
        $i++;
    }
}
} else {
    foreach ($commands as $key => $value) {
        if ($value['descr'] == 'Main') {
            $defined[$i] = mb_ucfirst($key);
            $i++;
        }
    }  
}

$i = 0;

foreach ($commands as $key => $value) {
    if ($value['descr'] == 'Setting') {
        $definedset[$i] = mb_ucfirst($key);
        $i++;
    }
}

$i = 0;

foreach ($commands as $key => $value) {
    if ($value['descr'] == 'ADM2') {
        $definedADM[$i] = mb_ucfirst($key);
        $i++;
    }
}

define('COMMANDS', $defined);
define('COMMANDSSETTING', $definedset);

$arr1 = $definedADM;
$arr2 = COMMANDSSETTING;
$arr1[3] = $arr2[1];

define('COMMANDSADM', $arr1);

if (empty($select)) {
    require_once __DIR__ . '/../commands/HelloCommand.php';
    $obj = new HelloCommand($userId, $db, $message);
    $obj->start();
    exit('ok');
}

if(empty($db->query('SELECT * FROM users WHERE userId = '.$userId.' AND username = \''.('@'.$message['from']['username']).'\'')->fetch_array())) {
    $db->query('UPDATE users SET username = \'' .('@'.$message['from']['username']). '\' WHERE userId = '.$userId);
    $db->query('UPDATE orders SET link = (SELECT username FROM users WHERE userId = \''.$userId.'\') WHERE userId = '.$userId);
}

$exp = explode(',',$message['text']);

if(count($exp)==4) {
    require_once __DIR__ . '/../commands/CreateOrderWithoutMenuCommand.php';
    $obj = new CreateOrderWithoutMenuCommand($userId,$db,$message,$exp);
    $obj->start();
    exit('ok');
}

$step = $select['step'];

if ($step == 'REG1' || $step == 'REG2' || $step == 'REG3' || $step == 'REG4') {
    require_once __DIR__ . '/../commands/RegisterCommand.php';
    $obj = new RegisterCommand($userId, $db, $message);
    $obj->start();
    exit('ok');
} else if ($step == 'DATE1' || $step == 'DATE2') {
    require_once __DIR__ . '/../commands/DateCommand.php';
    $obj = new DateCommand($userId, $db, $message);
    $obj->start();
    exit('ok');
} else if ($step == 'CREATEORDER1' || $step == 'CREATEORDER2' || $step == 'CREATEORDER3' || $step == 'CREATEORDER4' || $step == 'CREATEORDER5' || $step == 'CREATEORDER6') {
    require_once __DIR__ . '/../commands/CreateOrderCommand.php';
    $obj = new CreateOrderCommand($userId, $db, $message);
    $obj->start();
    exit('ok');
} else if ($step == 'SEARCHORDER1' || $step == 'SEARCHORDER2' || $step == 'SEARCHORDER3') {
    require_once __DIR__ . '/../commands/SearchOrderCommand.php';
    $obj = new SearchOrderCommand($userId, $db, $message);
    $obj->start();
    exit('ok');
} else if($step == 'ADMBAN' && $select['grade'] == 'ADM') {
    require_once __DIR__ . '/../commands/BanCommand.php';
    $obj = new BanCommand($userId,$db,$message);
    $obj->start();
    exit('ok');
} else if($step == 'ADMDEL' && $select['grade'] == 'ADM') {
    require_once __DIR__ . '/../commands/GetOrdersCommand.php';
    $obj = new GetOrdersCommand($userId,$db,$message);
    $obj->start();
    exit('ok');
}

if($commands[$text] && $select['grade'] == 'ADM' && ($commands[$text]['descr'] == 'ADM2' || $commands[$text]['descr'] == 'ADM1')) {
    $value = $commands[$text];
    require_once __DIR__ . '/../commands/' . $value['class'] . 'Command.php';
    $str = $value['class'] . 'Command';
    $obj = new $str($userId, $db,$message);
    $obj->start();
    exit('ok');
} elseif($commands[$text] && $select['grade'] == 'User' && ($commands[$text]['descr'] == 'ADM2' || $commands[$text]['descr'] == 'ADM1')) {
    require_once __DIR__ . '/../commands/ErrorCommand.php';
    $obj = new ErrorCommand($userId,1);
    $obj->start();
    exit('ok');
}

if ($text == '/register') {
    require_once __DIR__ . '/../commands/RegisterCommand.php';
    $obj = new RegisterCommand($userId, $db);
    $obj->start();
    exit('ok');
} else if ($commands[$text] && $select['registered']) {
    $value = $commands[$text];
    require_once __DIR__ . '/../commands/' . $value['class'] . 'Command.php';
    $str = $value['class'] . 'Command';
    $obj = new $str($userId, $db,$message);
    $obj->start();
    exit('ok');
} else if ($commands[$text] && !($select['registered'])) {
    require_once __DIR__ . '/../commands/ErrorCommand.php';
    $obj = new ErrorCommand($userId,1);
    $obj->start();
    exit('ok');
}
