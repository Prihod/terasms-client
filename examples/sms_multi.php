<?php

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use TeraSMS\Client;
use TeraSMS\Exception\RequestException;
use TeraSMS\Request\MultiSMSRequest;
use TeraSMS\Request\SMSRequest;

$login = 'user';
$token = '2LsWvGkYYOjyPw3GWbp5L';
$client = new Client($login, $token);

try {
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

    $response = $client->execute($multiRequest);

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
