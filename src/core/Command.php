<?php

require_once 'Telegram.php';

class Command
{

    protected $id;
    protected $db;
    private $api;

    protected function __construct($id,$db = NULL)
    {
        $this->id = $id;
        $this->api = new Telegram('833368853:AAHAG9DKEi2XmduyKdFcBELQxnbPqoi-238');
        if($db!=NULL) {
            $this->db = $db;
            mysqli_query($this->db,"SET NAMES UTF8");
        }
    }

    protected function select($table) {
        if($this->db->query('SELECT * FROM '.$table.' WHERE userId = ' . $this->id)) {
        return $this->db->query('SELECT * FROM '.$table.' WHERE userId = ' . $this->id)->fetch_assoc();
        }
    }
    
    protected function escape($text) {
        return mysqli_real_escape_string($this->db,$text);
    }

    protected function sendText($text)
    {
        $this->api->send($this->id,$text,'none');
    }

    protected function sendHtml($text)
    {
        $this->api->send($this->id,$text,'HTML');
    }

    protected function showKeyboard($opt,$text,$array)
    {
        $arrayToSend = $array;
            for ($i = 0; $i < count($array); $i++) {
                $arrayToSend[$i] = array($array[$i]);
            }
        $this->api->keyboard($opt,$this->id,$arrayToSend,$text);
    }

    protected function messageButtons($text,$array)
    {
        $this->api->messageButtons($this->id,$array,$text);
    }
}
