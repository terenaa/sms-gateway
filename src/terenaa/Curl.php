<?php
/**
 * Wrapper for cURL requests
 *
 * PHP version 5
 *
 * @category    Utils
 * @author      Krzysztof Janda <k.janda@the-world.pl>
 * @license     https://opensource.org/licenses/MIT MIT
 * @version     1.1
 * @link        https://www.github.com/terenaa/sms-gateway
 *
 */

namespace terenaa\SmsGateway;


/**
 * Class Curl
 * @package terenaa\SmsGateway
 */
class Curl
{
    private $ch;

    /**
     * Curl constructor.
     */
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

        if (($error = curl_error($this->ch))) {
            throw new SmsGatewayException("cURL error: {$error}");
        }
    }

    /**
     * Sends a single cURL request.
     *
     * @param string $url URL to open
     * @param array $params params to send via POST
     * @return mixed
     * @throws SmsGatewayException
     */
    public function sendRequest($url, array $params)
    {
        curl_setopt_array($this->ch, array(
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => http_build_query($params)
        ));

        $response = curl_exec($this->ch);

        if (($error = curl_error($this->ch))) {
            throw new SmsGatewayException("cURL error: {$error}");
        }

        return $response;
    }

    /**
     * Gets a value of input field of given name from HTML passed by first parameter.
     *
     * @param string $html HTML string
     * @param string $propertyName property name to find
     * @return mixed
     */
    public function getHtmlProperty($html, $propertyName)
    {
        if (!preg_match('/name=\"' . $propertyName . '\".*?value=\"([^\"]+)\"/', $html, $matches)) {
            preg_match('/value=\"([^\"]+)\".*?name=\"' . $propertyName . '\"/', $html, $matches);
        }

        return isset($matches[1]) ? $matches[1] : null;
    }

    /**
     * Closes cURL.
     */
    public function close()
    {
        if ($this->ch) {
            curl_close($this->ch);
        }
    }
}
