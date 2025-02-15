<?php

namespace App\Http\Controllers\Hooks;

use App\Helpers\Telegram\Telegram;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ApproveController extends Controller
{
    private Telegram $telegram;

    private string $dialogName = 'approvment';

    public function handler(Request $request): string
    {
        $telegramData = $request->post();

        if (!empty($telegramData) && isset($telegramData['update_id'])) {

            if (isset($telegramData['message']['text'])) { // получено сообщение

                if (str_starts_with($telegramData['message']['text'], '/')) { // команда
                    $this->processCmd($telegramData);
                } else {  // просто сообщение
                    $this->processMsg($telegramData);
                }

            } else if (isset($telegramData['callback_query'])) { // нажата кнопка

                $this->processBtn($telegramData);

            } else {
                logs()->error('Telegram else');
            }

        }

        return 'ok';
    }


    private function processMsg($telegramData)
    {

//        // получить id сообщения
//        $telegramMsgId = null;
//        if (isset($telegramData['message']['message_id'])) {
//            $telegramMsgId = [
//                'telegram' => $telegramData['message']['message_id']
//            ];
//        }

        // получить id телеграмм пользователя
        $telegramUserId = $telegramData['message']['from']['id'];
//        $update_id = $telegramData['update_id'];

        // Если не зарегистрирован - вывести сообщение с предложением старта
        $user = User::whereTgId($telegramUserId)->first();
        if (empty($user)) {
            $msg = '/start';
            return $this->sendMsg($telegramUserId, $msg);
        } else { // Определить состояние диалога. Обработать сообщение в соответствии с состоянием диалога
            $dialogState = $user->getTgDialogState($this->dialogName);
            switch ($dialogState) {
                case 'start':
                    break;
//                default:

            }
        }
    }

    private function processCmd($telegramData)
    {

        $cmd = trim((string)$telegramData['message']['text']);

        switch ($cmd) {
            case '/start':
                $a = 5;
                break;
            default:
                break;
        }


        // команды:
        // /start - зарегистрировать пользователя, если его еще нет
        // /report - вывести меню заполнения отчетности по утверждаемым


        $Telegram = new Telegram(new Http(), env('TELEGRAM_TOKEN'));

        $telegramQuery = $telegramData['callback_query'];
        $telegramUserId = $telegramQuery['from']['id'];
        $user = User::whereTgId($telegramUserId)->first();

        if ($user) {
            $telegramQueryData = $telegramQuery['data'];
            $message = $telegramQuery['message'];
            $btnReply = $message['reply_markup'];

            $btnReply = $this->checkBtnClick($btnReply, $telegramQueryData);
            $resEdit = $Telegram->editMessage($telegramUserId, $message['text'], $message['message_id'], json_encode($btnReply));

            $config = config('telegram.logicBot.' . $telegramQuery['data']);

            $telegramObject = [
                'telegram' => $Telegram,
                'user' => $user,
                'update' => $update_id,
            ];
            $msgObject = [
                'text' => $telegramQueryData,
                'id' => null,
                'file' => $file,
                'typing' => null,
            ];
            $res = $this->configActions($telegramObject, $config, $msgObject);
        } else {
            $res = false;
        }
    }

    private function processBtn(array|string $telegramData)
    {

        $telegramQuery = $telegramData['callback_query'];
        $telegramUserId = $telegramQuery['from']['id'];
        $user = User::whereTgId($telegramUserId)->first();

        if ($user) {
            $telegramQueryData = $telegramQuery['data'];
            $message = $telegramQuery['message'];
            $btnReply = $message['reply_markup'];

            $btnReply = $this->checkBtnClick($btnReply, $telegramQueryData);
            $resEdit = $Telegram->editMessage($telegramUserId, $message['text'], $message['message_id'], json_encode($btnReply));

            $config = config('telegram.logicBot.' . $telegramQuery['data']);

            $telegramObject = [
                'telegram' => $Telegram,
                'user' => $user,
                'update' => $update_id,
            ];
            $msgObject = [
                'text' => $telegramQueryData,
                'id' => null,
                'file' => $file,
                'typing' => null,
            ];
            $res = $this->configActions($telegramObject, $config, $msgObject);
        } else {
            $res = false;
        }
    }


    private function sendMsg($telegramUserId, $msg, $buttons = null): Response
    {

//        $sendMsg = 'ага';
//
//        $buttons = [
//            'inline_keyboard' => [
//                [
//                    [
//                        'text' => 'Новый сайт',
//                        'callback_data' => 'addManager',
//                    ],
//                ]
//            ]
//        ];


        return $this->connect()->sendMessage($telegramUserId, $msg, $buttons);
    }

//    private function sendMsg($telegramUserId, $msg, $mode = null)
//    {
//        if (!empty($mode)) {
//            $ar = [
//                'approvment',
//                $mode,
//                $msg
//            ];
//        } else {
//            $ar = [
//                'approvment',
//                $msg
//            ];
//        }
//        __(implode('.', $ar));
////        __('approvment.Profile.Enter the Phone Number');
//
//    }

    private function connect(): Telegram
    {
        if (empty($this->telegram)) {
            $this->telegram = new Telegram(new Http(), env('TELEGRAM_TOKEN'));
        }
        return $this->telegram;
    }

}
