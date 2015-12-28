# SMS Gateway
Just another simple PHP SMS Gateway

Alternative, independently developed version: [Dreamer1258/sms-gateway](https://github.com/Dreamer1258/sms-gateway)

## Installation
```
composer require terenaa/sms-gateway
```

## Examples
### Basic
Sends single text message.

```php
require_once __DIR__ . '/vendor/autoload.php';

use terenaa\SmsGateway\SmsGateway;

try {
    $sms = new SmsGateway();
    $sms->send('123456789', 'Test nowej wersji');
} catch (SmsGatewayException $e) {
    echo $e->getMessage();
}
```

### Multiple recipients
Sends single text message to multiple recipients

```php
require_once __DIR__ . '/vendor/autoload.php';

use terenaa\SmsGateway\SmsGateway;

try {
    $sms = new SmsGateway();
    $sms->sendMultiple(array('123456789', '234567891'), 'The message');
} catch (SmsGatewayException $e) {
    echo $e->getMessage();
}
```

### Bash alias with options
Sends single text message from terminal

```bash
alias sms="/home/USERNAME/Scripts/SMSGateway/sms.php"
```

```php
// /home/USERNAME/Scripts/SMSGateway/sms.php

require_once __DIR__ . '/vendor/autoload.php';

use terenaa\SmsGateway\SmsGateway;

$opts = getopt('p:m:s::b::', array('phone:', 'msg:', 'sig::', 'phoneback::'));
$phone = fetchOpt($opts, array('p', 'phone'));
$msg = fetchOpt($opts, array('m', 'msg'));
$sig = fetchOpt($opts, array('s', 'sig'));
$phoneback = fetchOpt($opts, array('b', 'phoneback'));

if (!$phone || !$msg) {
    echo "Usage: sms --phone=<phone> --msg=<message> [--sig=<signature> [--phoneback=<phone back>]]\n";
    exit;
}

$sms = new SmsGateway();

if ($sms->send($phone, $msg, $sig, $phoneback)) {
    echo "Message has been sent.\n";
} else {
    echo "Something went wrong...\n";
}

function fetchOpt($opts, array $names)
{
    foreach ($names as $name) {
        if (isset($opts[$name])) {
            return $opts[$name];
        }
    }
    
    return null;
}
```