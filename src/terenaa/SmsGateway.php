<?php

/**
 * Just another simple PHP SMS Gateway
 *
 * PHP version 5
 *
 * @category    Utils
 * @author      Dominik Marcak <dominik.marcak@gmail.com>
 * @author      Krzysztof Janda <k.janda@the-world.pl>
 * @license     https://opensource.org/licenses/MIT MIT
 * @version     1.1
 * @link        https://www.github.com/terenaa/sms-gateway
 *
 */

namespace terenaa\SmsGateway;


class SmsGateway
{
    const ENDPOINT = 'http://darmowabramkasms.net/index.php';

    public function __construct()
    {
    }

    public function send($number, $message, $signature = null, $phoneback = null)
    {
        $curl = new Curl();
        $sent = false;

        try {
            // Step 2
            $responseStep2 = $curl->sendRequest(self::ENDPOINT . '?page=sendsms', array(
                'phoneno' => $number,
                'message' => $message,
                'signature' => $signature,
                'phoneback' => $phoneback,
                'action' => 'verify',
                'ads_check1' => 'js_off',
                'ads_check2' => 'js_off'
            ));
            $phpsessid = $curl->getHtmlProperty($responseStep2, 'PHPSESSID');

            // Step 3
            $curl->sendRequest(self::ENDPOINT, array(
                'PHPSESSID' => $phpsessid,
                'action' => 'confirmbyuser'
            ));

            // Step 4
            $responseStep4 = $curl->sendRequest(self::ENDPOINT, array(
                'operator' => 'donotknow',
                'action' => 'confirmprovider'
            ));
            $imagecode = $curl->getHtmlProperty($responseStep4, 'imgcode');

            // Step 5
            $curl->sendRequest(self::ENDPOINT . '?a=sent', array(
                'imgcode' => $imagecode,
                'action' => 'useraccepted'
            ));

            $sent = true;
        } catch (SmsGatewayException $e) {
            echo $e->getMessage();
        }

        $curl->close();
        return $sent;
    }

    public function sendMultiple(array $numbers, $message, $signature = null, $phoneback = null)
    {
        $sent = 0;

        foreach ($numbers as $number) {
            $sent += $this->send($number, $message, $signature, $phoneback);
            sleep(1);
        }

        return $sent;
    }
}
