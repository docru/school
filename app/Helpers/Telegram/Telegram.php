<?php

namespace App\Helpers\Telegram;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class Telegram
{

    protected $http;
    protected $bot;
    const url = 'https://api.telegram.org/bot';
    const urlFile = 'https://api.telegram.org/file/bot';

    /**
     * @param Http $http
     * @param $bot
     */
    public function __construct(Http $http, $bot)
    {
        $this->http = $http;
        $this->bot = $bot;
    }


    /**
     * @return Response
     */
    public function getWebhook(): Response
    {
        return $this->http::get(self::url . $this->bot . '/getWebhookInfo');
    }


    /**
     * @param $route
     * @return Response
     */
    public function setWebhook($route): Response
    {
        return $this->http::get(self::url . $this->bot . '/setWebhook?url=' . $route);
    }


    /**
     * @return Response
     */
    public function deleteWebhook(): Response
    {
        return $this->http::get(self::url . $this->bot . '/deleteWebhook');
    }


    public function prepareMsg($msg)
    {
        $pos = strpos($msg, '<s');
        $posEnd = strpos($msg, 't>');

        if ($pos !== false && $posEnd !== false) {
            $scr = substr($msg, $pos, ($posEnd + 2) - $pos);
            $msg = explode($scr, $msg);

            $scr = str_replace('<', '&lt;', $scr);
            $scr = str_replace('>', '&gt;', $scr);

            return implode('<pre>' . $scr . '</pre>', $msg);
        } else {
            return $msg;
        }
    }


    /**
     * @param $chatId
     * @param $message
     * @return Response
     */
    public function sendMessage($chatId, $message, $buttons = null): Response
    {

        $message = $this->prepareMsg($message);

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'html',
        ];

        if (!empty($buttons)) {
            $data = array_merge($data, ['reply_markup' => $buttons]);
        }

        return $this->http::post(self::url . $this->bot . '/sendMessage', $data);
    }


    /**
     * @param $chatId
     * @param $message
     * @param $messageId
     * @param $buttons
     * @return Response
     */
    public function editMessage($chatId, $message, $messageId, $buttons = null): Response
    {

        $message = $this->prepareMsg($message);

        $data = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $message,
            'parse_mode' => 'html',
        ];

        if (!empty($buttons)) {
            $data = array_merge($data, ['reply_markup' => $buttons]);
        }

        return $this->http::post(self::url . $this->bot . '/editMessageText', $data);
    }


    public function getUserProfilePhotos($id)
    {
        $res = '';

        $data = [
            'user_id' => $id,
            'offset' => 0,
            'limit' => 1,
        ];

        $resPhoto = $this->http::post(self::url . $this->bot . '/getUserProfilePhotos', $data);
        $resData = json_decode($resPhoto);

        if (isset($resData->result->photos[0][0])) {
            $res = $resData->result->photos[0][0]->file_id;
        }

        return $res;
    }


    public function getFile($id): string|Response
    {
        $data = [
            'file_id' => $id,
        ];

        $res = $this->http::post(self::url . $this->bot . '/getFile', $data);
        $resData = json_decode($res);

        if (isset($resData->result->file_path)) {
            $res = self::urlFile . $this->bot . '/' . $resData->result->file_path;
        }

        return $res;
    }


    /**
     * @param $chatId
     * @param $file
     * @param $text
     * @return Response
     */
    public function sendDocument($chatId, $file, $text, $local = false): Response
    {
        $data = [
            'chat_id' => $chatId,
            'document' => $file,
            'caption' => $text
        ];

        if ($local === true) {
            return $this->http::attach(substr($file, strrpos($file, '/') + 1), Storage::get('/public/' . $file), $file)
                ->post(self::url . $this->bot . '/sendDocument', $data);
        } else {
            return $this->http::post(self::url . $this->bot . '/sendDocument', $data);
        }
    }


    public function sendChatAction($chatId): Response
    {
        $data = [
            'chat_id' => $chatId,
            'action' => 'typing',
        ];

        return $this->http::post(self::url . $this->bot . '/sendChatAction', $data);
    }


}
