<?php
/**
 * Wrapper for cURL requests
 *
 * PHP version 5
 *
 * @category    Utils
 * @author      Krzysztof Janda <k.janda@the-world.pl>
 * @license     https://opensource.org/licenses/MIT MIT
 * @version     1.0
 * @link        https://www.github.com/terenaa/sms-gateway
 *
 */

namespace terenaa\SmsGateway;


class Curl
{
    private $ch;

    public function __construct()
    {
        $this->ch = curl_init();
        $cookie_file = '/tmp/' . date('d') . '_cookie.txt';
        curl_setopt_array($this->ch, array(
            CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/32.0.1700.107 Chrome/32.0.1700.107 Safari/537.36',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIESESSION => true,
            CURLOPT_COOKIEJAR => $cookie_file,
            CURLOPT_COOKIEFILE => $cookie_file
        ));
    }

    public function sendRequest($url, array $params)
    {
        curl_setopt_array($this->ch, array(
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => http_build_query($params)
        ));

        $response = curl_exec($this->ch);
        $error = curl_error($this->ch);

        if ($error) {
            throw new SmsGatewayException($error);
        }

        return $response;
    }

    public function getHtmlProperty($html, $propertyName)
    {
        if (!preg_match('/name=\"' . $propertyName . '\".*?value=\"([^\"]+)\"/', $html, $matches)) {
            preg_match('/value=\"([^\"]+)\".*?name=\"' . $propertyName . '\"/', $html, $matches);
        }

        return isset($matches[1]) ? $matches[1] : null;
    }

    public function close()
    {
        if ($this->ch) {
            curl_close($this->ch);
        }
    }
}
