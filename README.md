# TeraSMS-client

This is client library for the [terasms.ru](https://terasms.ru) API.

## 1. Prerequisites

* PHP 7.4 or later

## 2. Installation

The terasms-client client can be installed using Composer by running the following command:

```sh
composer require prihod/terasms-client
```

## 3. Initialization

Create Client object using the following code:

```php
<?php

use TeraSMS\Client;

require_once __DIR__ . '/vendor/autoload.php';

$login = 'user';
$token = '2LsWvGkYYOjyPw3GWbp5L';
$client = new Client($login,$token );
```
## 4. API Requests

### 4.1. Request get [Balance](https://terasms.ru/api-http-check-balance.html)

```php
use TeraSMS\Exception\RequestException;
use TeraSMS\Request\BalanceRequest;

try {
    $request = new BalanceRequest();
    $response = $client->execute($request);

    if ($response->isSuccess()) {
        echo "Response to array:\n";
        print_r($response->toArray());
        echo "Response get data:\n";
        print_r($response->getData());
    } else {
        echo "Response Status: {$response->getStatus()}";
        echo "Response Error: {$response->getError()}";
    }
} catch (RequestException $e) {
    echo "Exception: {$e->getMessage()}\n";
    print_r($e->request->toArray());
}
```
### 4.2. Request to send [SMS](https://terasms.ru/api-http.html)

```php
use TeraSMS\Client;
use TeraSMS\Exception\RequestException;
use TeraSMS\Request\SMSRequest;

try {
    $request = (new SMSRequest())
        ->setPhone('71234567890')
        ->setSender('SMSTest')
        ->setMessage('Message text to be sent via SMS');

    $response = $client->execute($request);
    if ($response->isSuccess()) {
        echo "Response to array:\n";
        print_r($response->toArray());
        echo "Response get data:\n";
        print_r($response->getData());
        echo "Response get all entries:\n";
        print_r($response->getEntries());
        echo "Response get first entry:\n";
        $entry = $response->getFirstEntry();
        if ($entry->isSuccess()) {
            echo "Entry to array:\n";
            print_r($entry->toArray());
            echo "Entry get data:\n";
            print_r($entry->getData());
        } else {
            echo "Entry Status: {$entry->getStatus()}";
            echo "Entry Error: {$entry->getError()}";
        }
    } else {
        echo "Response Status: {$response->getStatus()}";
        echo "Response Error: {$response->getError()}";
    }
} catch (RequestException $e) {
    echo "Exception: {$e->getMessage()}\n";
    print_r($e->request->toArray());
}
```

### 4.3. Multiple SMS sending request

```php
use TeraSMS\Request\MultiSMSRequest;
use TeraSMS\Request\SMSRequest;


 $request1 = (new SMSRequest())
        ->setId(1232)
        ->setPhone('71234567890')
        ->setSender('SMSTest')
        ->setMessage('Message text to be sent via SMS1');

    $request2 = (new SMSRequest())
        ->setId(1233)
        ->setPhone('71234567891')
        ->setSender('SMSTest')
        ->setMessage('Message text to be sent via SMS2');

    $multiRequest = (new MultiSMSRequest())
        ->append($request1)
        ->append($request2);

$response = $client->execute($requests);
...
```

### 4.4. Request status message for SMS, Viber, VK, «каскад»  

```php
use TeraSMS\Request\StatusRequest;

 $request = new StatusRequest([12323, 23262, 23264]);
   /**
    $request = (new StatusRequest())
        ->setMessageIds([12323, 23262, 23264]);

    $request = (new StatusRequest())
        ->appendMessageId(12323)
        ->appendMessageId(23262)
        ->appendMessageId(23264);

    $request = (new StatusRequest())
        ->setMessageIds([12323, 23262, 23264]);
   */

$response = $client->execute($request);
...
```


#### 4.5.  Request to send a message to [Viber](https://terasms.ru/api-http-viber.html)

```php
use TeraSMS\Request\ViberRequest;

$request = (new ViberRequest())
        ->setPhone('71234567890')
        ->setSender('ViberTest')
        ->setMessage('Message text to be sent via Viber')
        ->setImage('https://terasms.ru/logo.jpg')
        ->setLink('https://terasms.ru/')
        ->setButtonText('Text');

$response = $client->execute($request);
...
```


#### 4.6. Multiple Viber sending request

```php
use TeraSMS\Request\MultiViberRequest;
use TeraSMS\Request\ViberRequest;

$request1 = (new ViberRequest())
        ->setPhone('71234567890')
        ->setSender('ViberTest')
        ->setMessage('Message text to be sent via Viber')
        ->setImage('https://terasms.ru/logo.jpg')
        ->setLink('https://terasms.ru/')
        ->setButtonText('Text');

    $request2 = (new ViberRequest())
        ->setPhone('71234567891')
        ->setSender('ViberTest')
        ->setMessage('Message text to be sent via Viber')
        ->setImage('https://terasms.ru/logo.jpg')
        ->setLink('https://terasms.ru/')
        ->setButtonText('Text');

    $multiRequest = (new MultiViberRequest())
        ->append($request1)
        ->append($request2);

$response = $client->execute($request);
...
```

#### 4.7. Request to send [Vk](https://terasms.ru/api-http-vkontakte.html)

```php
use TeraSMS\Request\VkRequest;

 $request = (new VkRequest())
        ->setPhone('71234567890')
        ->setSender('VkTest')
        ->setMessage('Message text to be sent via Vk');

$response = $client->execute($request);
...
```

#### 4.8. Request to send [WhatsApp](https://terasms.ru/api-http-whatsapp.html)

```php
use TeraSMS\Request\WhatsAppRequest;

   $request = (new WhatsAppRequest())
        ->setPhone('71234567890')
        ->setSender('WhatsAppTest')
        ->setMessage('Message text to be sent via WhatsApp');

$response = $client->execute($request);
...
```

#### 4.9. Request to send [Cascade](https://terasms.ru/api-http-cascade.html)

```php
use TeraSMS\Request\CascadeRequest;

   $request = (new CascadeRequest())
        ->setPhone('71234567890')
        ->setSender('CascadeTest')
        ->setMessage('Message text to be sent via Cascade');

$response = $client->execute($request);
...
```

#### 4.10. Request to send [Flash Call](https://terasms.ru/api-http-callpass.html) (Call Password)

```php
use TeraSMS\Request\VoiceOTPRequest;

   $request = (new VoiceOTPRequest())
        ->setCode(2345)
        ->setPhone('71234567890')
        ->setSender('VoiceOTPTest');

$response = $client->execute($request);
...
```

## 5. Links

* API [docs](https://terasms.ru/api.html)
