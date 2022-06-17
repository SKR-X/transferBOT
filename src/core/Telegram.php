<?php

// Класс для работы с Telergram API

class Telegram
{

    private $token = '';

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function send($id, $message, $parse_mode)
    {
        $data = array(
            'chat_id'      => $id,
            'text'     => $message,
            'parse_mode' => $parse_mode,
        );
        $out = $this->request('sendMessage', $data);
        return $out;
    }

    public function keyboard($opt, $id, $array, $text)
    {
        $replyMarkup = array(
            'keyboard' => $array,
            'resize_keyboard' => $opt,
        );
        $encodedMarkup = json_encode($replyMarkup);
        $data = array(
            'chat_id' => $id,
            'reply_markup' => $encodedMarkup,
            'text' => $text,
            'parse_mode' => 'HTML',
        );
        $out = $this->request('sendMessage', $data);
        return $out;
    }

    public function messageButtons($id, $array, $text)
    {
        $arr = array(
            'inline_keyboard' => array(
                $array,
            ),
        );
        $encodedKeyboard = json_encode($arr);
        $data = array(
            'chat_id' => $id,
            'reply_markup' => $encodedKeyboard,
            'text' => $text,
            'parse_mode' => 'HTML',
        );
        $out = $this->request('sendMessage', $data);
        return $out;
    }

    public function request($method, $data = array())
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token .  '/' . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $out = json_decode(curl_exec($curl));

        curl_close($curl);

        return $out;
    }
}