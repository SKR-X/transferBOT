<?php

require_once __DIR__ . '/../core/Command.php';


class RegisterCommand extends Command
{

    private $message;
    private $select;

    public function __construct($id, $db, $message = NULL)
    {
        parent::__construct($id,$db);
        $this->message = $message;
        $this->select = $this->select('users');
    }

    public function start()
    {
        if(mb_strtolower($this->message['text']) == 'отмена') {
            $this->db->query('UPDATE users SET step = \'NONE\',FIO = \'\',drivePhotoId = \'\',autoPhotoId = \'\',autoClass = \'\' WHERE userId = ' . $this->id);
            $this->showKeyboard(TRUE,'<b>Команда отменена. Вызвать заново - </b> /register',array('/register'));
            return TRUE;
        }
        $this->select = $this->select('users');
        if ($this->select['registered']) {
            $this->sendHtml('<b>Вы уже зарегистрированны!</b>');
            return TRUE;
        }
            switch ($this->select['step']) {
                case 'NONE':
                    $this->db->query('UPDATE users SET step = \'REG1\' WHERE userId = ' . $this->id);
                    $this->showKeyboard(TRUE,'<b>Введите ФИО:</b>',array('Отмена'));
                    return TRUE;
                case 'REG1':
                    if(!isset($this->message['text'])) {
                        $this->sendHtml('<b>Введите ФИО:</b>');
                        return TRUE;
                    } else {
                    $this->db->query('UPDATE users SET FIO = \'' . $this->escape($this->message['text']) . '\' WHERE userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'REG2\' WHERE userId = ' . $this->id);
                    $this->sendHtml('<b>Фото водительского удостоверения:</b>');
                    return TRUE;
                }
            case 'REG2':
                if ($this->message['photo'][0]['file_id'] != NULL) {
                    $this->db->query('UPDATE users SET drivePhotoId = \'' . $this->message['photo'][0]['file_id'] . '\' WHERE userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'REG3\' WHERE userId = ' . $this->id);
                    $this->sendHtml('<b>Фото автомобиля:</b>');
                } else {
                    $this->sendHtml('<b>Фото водительского удостоверения:</b>');
                }
                return TRUE;
            case 'REG3':
                if ($this->message['photo'][0]['file_id'] != NULL) {
                    $this->db->query('UPDATE users SET autoPhotoId = \'' . $this->message['photo'][0]['file_id'] . '\' WHERE userId = ' . $this->id);
                    $this->db->query('UPDATE users SET step = \'REG4\' WHERE userId = ' . $this->id);
                    $this->sendHtml('<b>Класс автомобиля(введите цифру):</b>' . PHP_EOL . '1 - Эконом' . PHP_EOL . '2 - Комфорт' . PHP_EOL . '3 - Комфорт +' . PHP_EOL . '4 - Минивен (микроавтобус)' . PHP_EOL . '5 - Бизнес');
                } else {
                    $this->sendHtml('<b>Фото автомобиля:</b>');
                }
                return TRUE;
            case 'REG4':
                if (!isset($this->message['text'])) {
                    $this->sendHtml('<b>Класс автомобиля(введите цифру):</b>');
                    return TRUE;
                } else {
                    switch ($this->message['text']) {
                        case 1:
                            $this->db->query('UPDATE users SET registered = 1, username = \'' . ('@' . $this->message['from']['username']) . '\', autoClass = \'Эконом\' WHERE userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                            $this->showKeyboard(TRUE, '<b>Успешная регистрация!</b>', COMMANDS);
                            $this->sendHtml('Команда для вывода главного меню - "Главное меню"' . PHP_EOL . 'Команда для отмены выполнения команды - "Отмена"');
                            return TRUE;
                        case 2:
                            $this->db->query('UPDATE users SET registered = 1, username = \'' . ('@' . $this->message['from']['username']) . '\', autoClass = \'Комфорт\' WHERE userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                            $this->showKeyboard(TRUE, '<b>Успешная регистрация!</b>', COMMANDS);
                            $this->sendHtml('Команда для вывода главного меню - "Главное меню"' . PHP_EOL . 'Команда для отмены выполнения команды - "Отмена"');
                            return TRUE;
                        case 3:
                            $this->db->query('UPDATE users SET registered = 1, username = \'' . ('@' . $this->message['from']['username']) . '\', autoClass = \'Комфорт +\' WHERE userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                            $this->showKeyboard(TRUE, '<b>Успешная регистрация!</b>', COMMANDS);
                            $this->sendHtml('Команда для вывода главного меню - "Главное меню"' . PHP_EOL . 'Команда для отмены выполнения команды - "Отмена"');
                            return TRUE;
                        case 4:
                            $this->db->query('UPDATE users SET registered = 1, username = \'' . ('@' . $this->message['from']['username']) . '\', autoClass = \'Минивен(микроавтобус)\' WHERE userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                            $this->showKeyboard(TRUE, '<b>Успешная регистрация!</b>', COMMANDS);
                            $this->sendHtml('Команда для вывода главного меню - "Главное меню"' . PHP_EOL . 'Команда для отмены выполнения команды - "Отмена"');
                            return TRUE;
                        case 5:
                            $this->db->query('UPDATE users SET registered = 1, username = \'' . ('@' . $this->message['from']['username']) . '\', autoClass = \'Бизнес\' WHERE userId = ' . $this->id);
                            $this->db->query('UPDATE users SET step = \'NONE\' WHERE userId = ' . $this->id);
                            $this->showKeyboard(TRUE, '<b>Успешная регистрация!</b>', COMMANDS);
                            $this->sendHtml('Команда для вывода главного меню - "Главное меню"' . PHP_EOL . 'Команда для отмены выполнения команды - "Отмена"');
                            return TRUE;
                        default:
                            $this->sendHtml('<b>Класс автомобиля(введите цифру):</b>');
                            return TRUE;
                    }
                }
        }
    }
}
