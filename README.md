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
    $sms->send('123456789', 'The message');
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
use terenaa\SmsGateway\SmsGatewayException;

$opts = getopt('p:m:s::b::', array('phone:', 'msg:', 'sig::', 'phoneback::'));
$phone = fetchOpt($opts, array('p', 'phone'));
$msg = fetchOpt($opts, array('m', 'msg'));
$sig = fetchOpt($opts, array('s', 'sig'));
$phoneback = fetchOpt($opts, array('b', 'phoneback'));

if (!$phone || !$msg) {
    echo "Usage: sms --phone=<phone> --msg=<message> [--sig=<signature> [--phoneback=<phone back>]]\n";
    exit;
}

try {
    $sms = new SmsGateway();
    
    if ($sms->send($phone, $msg, $sig, $phoneback)) {
        echo "Message has been sent.\n";
    } else {
        echo "Something went wrong...\n";
    }
} catch (SmsGatewayException $e) {
    echo $e->getMessage();
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

### Contact book
The previous example extended with a phone book.
```php
//require_once __DIR__ . '/vendor/autoload.php';

use terenaa\SmsGateway\Contact;
use terenaa\SmsGateway\SmsGateway;
use terenaa\SmsGateway\SmsGatewayException;

$opts = getopt('p:m:s::b::c::', array('phone:', 'msg:', 'sig::', 'phoneback::', 'contact::', 'save'));
$phone = fetchOpt($opts, array('p', 'phone'));
$msg = fetchOpt($opts, array('m', 'msg'));
$sig = fetchOpt($opts, array('s', 'sig'));
$phoneback = fetchOpt($opts, array('b', 'phoneback'));
$name = fetchOpt($opts, array('c', 'contact'));
$save = fetchOpt($opts, array('save'));

if ((!$phone && !$name) || !$msg) {
    echo "Usage:\n\n"
        . "    sms -p|-c phone number or contact's name -m message [-s signature]\n\t[-b phoneback] [-c contact name] [-s]\n\n"
        . "OPTIONS\n"
        . "\t-p, --phone=NUMBER\n"
        . "\t\trecipient's phone number, required\n\n"
        . "\t-m, --msg=MESSAGE\n"
        . "\t\tthe message, required\n\n"
        . "\t-s, --sig=SIGNATURE\n"
        . "\t\tsender's signature\n\n"
        . "\t-b, --phoneback=NUMBER\n"
        . "\t\tsender's phone number\n\n"
        . "\t-c, --contact=NAME\n"
        . "\t\tnew contact's name (only if -s option used)\n\n"
        . "\t--save\n"
        . "\t\t save new contact\n\n";
    exit;
}

try {
    $sms = new SmsGateway();
    $contact = new Contact();

    if ($name && !$phone) {
        $phone = $contact->getPhone($name);

        if ($phone) {
            echo "Found {$name}'s number ({$phone}).\n";
        } else {
            echo "Cannot find '{$name}' in your phone book.\n";
            exit;
        }
    }

    if ($sms->send($phone, $msg, $sig, $phoneback)) {
        echo "Message has been sent.\n";
    } else {
        echo "Something went wrong...\n";
    }

    if ($save && $name && $phone) {
        $saved = $contact->create($phone, $name);

        if ($saved) {
            echo "Contact '{$name}' has been saved.\n";
        } else {
            echo "Cannot save contact.\n";
        }
    }
} catch (SmsGatewayException $e) {
    echo $e->getMessage();
}

function fetchOpt($opts, array $names)
{
    foreach ($names as $name) {
        if (isset($opts[$name])) {
            return $opts[$name] ? $opts[$name] : true;
        }
    }

    return null;
}
```
