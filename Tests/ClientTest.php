<?php

namespace TeraSMS\Tests;

use TeraSMS\Client;
use TeraSMS\Request\MultiSMSRequest;
use TeraSMS\Request\MultiViberRequest;
use TeraSMS\Request\SMSRequest;
use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TeraSMS\Request\StatusRequest;
use TeraSMS\Request\ViberRequest;
use TeraSMS\Request\VoiceOTPRequest;

class ClientTest extends TestCase
{
    private const TEST_LOGIN = 'user';
    private const TEST_PASSWORD = 'Pw3GWbp5L';
    private const TEST_TOKEN = '2LsWvGkYYOjyPw3GWbp5L';
    private const TEST_SENDER = 'Sender';
    private const TEST_MESSAGE_1 = 'Message 1';
    private const TEST_MESSAGE_2 = 'Message 2';
    private const TEST_TARGET_1 = '380971234567';
    private const TEST_TARGET_2 = '380971234568';
    private const TEST_MESSAGE_ID_1 = 3563;
    private const TEST_MESSAGE_ID_2 = 3564;


    private array $container = [];
    private MockHandler $mock;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = [];
        $this->mock = new MockHandler();
        $history = Middleware::history($this->container);
        $handlerStack = HandlerStack::create($this->mock);
        $handlerStack->push($history);

        $guzzle = new HTTPClient(
            [
                'handler' => $handlerStack,
                'base_uri' => Client::API_URL,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        $this->client = new Client(self::TEST_LOGIN, self::TEST_TOKEN);
        $reflection = new \ReflectionClass($this->client);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->client, $guzzle);
    }

    public function testSuccessfulSMSSending()
    {
        $responseApiData = [
            'status' => 0,
            'status_description' => 'OK',
            'message_infos' => [
                [
                    'status' => 0,
                    'id' => self::TEST_MESSAGE_ID_1,
                    'msisdn' => self::TEST_TARGET_1,
                ]
            ],
        ];


        $responseData = [
            'success' => true,
            'status' => 0,
            'error' => '',
            'data' => [
                [
                    'status' => 0,
                    'id' => self::TEST_MESSAGE_ID_1,
                    'msisdn' => self::TEST_TARGET_1,
                ]
            ],

        ];

        $this->mock->append(new Response(200, [], json_encode($responseApiData)));

        $request = (new SMSRequest())
            ->setId(self::TEST_MESSAGE_ID_1)
            ->setPhone(self::TEST_TARGET_1)
            ->setSender(self::TEST_SENDER)
            ->setMessage(self::TEST_MESSAGE_1);


        $response = $this->client->execute($request);
        $requestInfo = $this->getLastRequest();
        $requestBody = $this->getRequestBody($requestInfo);
        $this->assertRequestMethod($requestInfo);
        $this->assertRequestPath('/outbox/send/json', $requestInfo);

        $this->assertEquals('sms', $requestBody['type']);
        $this->assertEquals(self::TEST_TARGET_1, $requestBody['target']);
        $this->assertEquals(self::TEST_MESSAGE_1, $requestBody['message']);
        $this->assertEquals(self::TEST_SENDER, $requestBody['sender']);
        $this->assertEquals(self::TEST_MESSAGE_ID_1, $requestBody['sms_id']);
        $this->assertRequestAuthByToken($requestBody);
        $this->assertResponseData($responseData, $response);
    }

    public function testSuccessfulMultiSMSSending()
    {
        $responseApiData = [
            [
                'sms_id' => self::TEST_MESSAGE_ID_1,
                'message_id' => '365214379'
            ],
            [
                'sms_id' => self::TEST_MESSAGE_ID_2,
                'message_id' => '365214381'
            ],
        ];


        $responseData = [
            'success' => true,
            'status' => 0,
            'error' => '',
            'data' => [
                [
                    'sms_id' => self::TEST_MESSAGE_ID_1,
                    'message_id' => '365214379',
                ],
                [
                    'sms_id' => self::TEST_MESSAGE_ID_2,
                    'message_id' => '365214381',
                ]
            ],

        ];

        $this->mock->append(new Response(200, [], json_encode($responseApiData)));

        $request1 = (new SMSRequest())
            ->setId(self::TEST_MESSAGE_ID_1)
            ->setPhone(self::TEST_TARGET_1)
            ->setSender(self::TEST_SENDER)
            ->setMessage(self::TEST_MESSAGE_1);

        $request2 = (new SMSRequest())
            ->setId(self::TEST_MESSAGE_ID_2)
            ->setPhone(self::TEST_TARGET_2)
            ->setSender(self::TEST_SENDER)
            ->setMessage(self::TEST_MESSAGE_2);


        $multiRequest = (new MultiSMSRequest())
            ->append($request1)
            ->append($request2);

        $response = $this->client->execute($multiRequest);

        $requestInfo = $this->getLastRequest();
        $requestBody = $this->getRequestBody($requestInfo);
        $this->assertRequestMethod($requestInfo);
        $this->assertRequestPath('/outbox/msend_json', $requestInfo);

        $this->assertCount(2, $requestBody['smsPackage']);
        $this->assertEquals(self::TEST_SENDER, $requestBody['smsPackage'][0]['sender']);
        $this->assertEquals(self::TEST_TARGET_1, $requestBody['smsPackage'][0]['target']);
        $this->assertEquals(self::TEST_MESSAGE_1, $requestBody['smsPackage'][0]['message']);
        $this->assertEquals(self::TEST_MESSAGE_ID_1, $requestBody['smsPackage'][0]['sms_id']);
        $this->assertEquals(self::TEST_SENDER, $requestBody['smsPackage'][1]['sender']);
        $this->assertEquals(self::TEST_TARGET_2, $requestBody['smsPackage'][1]['target']);
        $this->assertEquals(self::TEST_MESSAGE_2, $requestBody['smsPackage'][1]['message']);
        $this->assertEquals(self::TEST_MESSAGE_ID_2, $requestBody['smsPackage'][1]['sms_id']);

        $this->assertRequestAuthByToken($requestBody);
        $this->assertResponseData($responseData, $response);
    }

    public function testSuccessfulViberSending()
    {
        $responseApiData = [
            'status' => 0,
            'status_description' => 'OK',
            'message_infos' => [
                [
                    'status' => 0,
                    'id' => self::TEST_MESSAGE_ID_1,
                    'msisdn' => self::TEST_TARGET_1,
                ]
            ],
        ];


        $responseData = [
            'success' => true,
            'status' => 0,
            'error' => '',
            'data' => [
                [
                    'status' => 0,
                    'id' => self::TEST_MESSAGE_ID_1,
                    'msisdn' => self::TEST_TARGET_1,
                ]
            ],

        ];

        $this->mock->append(new Response(200, [], json_encode($responseApiData)));

        $request = (new ViberRequest())
            ->setPhone(self::TEST_TARGET_1)
            ->setSender(self::TEST_SENDER)
            ->setMessage(self::TEST_MESSAGE_1);


        $response = $this->client->execute($request);
        $requestInfo = $this->getLastRequest();
        $requestBody = $this->getRequestBody($requestInfo);
        $this->assertRequestMethod($requestInfo);
        $this->assertRequestPath('/outbox/send/json', $requestInfo);

        $this->assertEquals('viber', $requestBody['type']);
        $this->assertEquals(self::TEST_TARGET_1, $requestBody['target']);
        $this->assertEquals(self::TEST_MESSAGE_1, $requestBody['message']);
        $this->assertEquals(self::TEST_SENDER, $requestBody['sender']);
        $this->assertRequestAuthByToken($requestBody);
        $this->assertResponseData($responseData, $response);
    }

    public function testSuccessfulViberWithMediaSending()
    {
        $responseApiData = [
            'status' => 0,
            'status_description' => 'OK',
            'message_infos' => [
                [
                    'status' => 0,
                    'id' => self::TEST_MESSAGE_ID_1,
                    'msisdn' => self::TEST_TARGET_1,
                ]
            ],
        ];


        $responseData = [
            'success' => true,
            'status' => 0,
            'error' => '',
            'data' => [
                [
                    'status' => 0,
                    'id' => self::TEST_MESSAGE_ID_1,
                    'msisdn' => self::TEST_TARGET_1,
                ]
            ],

        ];

        $this->mock->append(new Response(200, [], json_encode($responseApiData)));

        $request = (new ViberRequest())
            ->setPhone(self::TEST_TARGET_1)
            ->setSender(self::TEST_SENDER)
            ->setMessage(self::TEST_MESSAGE_1)
            ->setImage('https://terasms.ru/logo.jpg')
            ->setLink('https://terasms.ru/')
            ->setButtonText('Text');


        $response = $this->client->execute($request);
        $requestInfo = $this->getLastRequest();
        $requestBody = $this->getRequestBody($requestInfo);
        $this->assertRequestMethod($requestInfo);
        $this->assertRequestPath('/outbox/send_viber', $requestInfo);

        $this->assertArrayNotHasKey('type', $requestBody);
        $this->assertEquals(self::TEST_TARGET_1, $requestBody['target']);
        $this->assertEquals(self::TEST_MESSAGE_1, $requestBody['message']);
        $this->assertEquals(self::TEST_SENDER, $requestBody['sender']);
        $this->assertRequestAuthByToken($requestBody);
        $this->assertResponseData($responseData, $response);
    }

    public function testSuccessfulMultiViberSending()
    {
        $responseApiData = [
            'messages' => [
                [
                    'status' => 0,
                    'message_id' => self::TEST_MESSAGE_ID_1,
                ],
                [
                    'status' => 0,
                    'message_id' => self::TEST_MESSAGE_ID_2,
                ]
            ],
        ];


        $responseData = [
            'success' => true,
            'status' => null,
            'error' => '',
            'data' => [
                [
                    'status' => 0,
                    'message_id' => self::TEST_MESSAGE_ID_1,
                ],
                [
                    'status' => 0,
                    'message_id' => self::TEST_MESSAGE_ID_2,
                ]
            ],

        ];

        $this->mock->append(new Response(200, [], json_encode($responseApiData)));

        $request1 = (new ViberRequest())
            ->setPhone(self::TEST_TARGET_1)
            ->setSender(self::TEST_SENDER)
            ->setMessage(self::TEST_MESSAGE_1)
            ->setImage('https://terasms.ru/logo.jpg')
            ->setLink('https://terasms.ru/')
            ->setButtonText('Text');

        $request2 = (new ViberRequest())
            ->setPhone(self::TEST_TARGET_2)
            ->setSender(self::TEST_SENDER)
            ->setMessage(self::TEST_MESSAGE_2)
            ->setImage('https://terasms.ru/logo.jpg')
            ->setLink('https://terasms.ru/')
            ->setButtonText('Text');

        $multiRequest = (new MultiViberRequest())
            ->append($request1)
            ->append($request2);


        $response = $this->client->execute($multiRequest);
        $requestInfo = $this->getLastRequest();
        $requestBody = $this->getRequestBody($requestInfo);
        $this->assertRequestMethod($requestInfo);
        $this->assertRequestPath('/outbox/send_viber_bulk/json', $requestInfo);

        $this->assertCount(2, $requestBody['messages']);
        $this->assertEquals(self::TEST_TARGET_1, $requestBody['messages'][0]['target']);
        $this->assertEquals(self::TEST_MESSAGE_1, $requestBody['messages'][0]['message']);
        $this->assertEquals(self::TEST_SENDER, $requestBody['messages'][0]['sender']);
        $this->assertEquals(self::TEST_TARGET_2, $requestBody['messages'][1]['target']);
        $this->assertEquals(self::TEST_MESSAGE_2, $requestBody['messages'][1]['message']);
        $this->assertEquals(self::TEST_SENDER, $requestBody['messages'][1]['sender']);
        $this->assertRequestAuthByToken($requestBody);
        $this->assertResponseData($responseData, $response);
    }

    public function testSuccessfulVoiceOTPSending()
    {
        $responseApiData = [
            'status' => 0,
            'status_description' => 'OK',
            'message_infos' => [
                [
                    'status' => null,
                    'id' => self::TEST_MESSAGE_ID_1,
                    'msisdn' => self::TEST_TARGET_1,
                    'price' => 0.5,
                ]
            ],
        ];


        $responseData = [
            'success' => true,
            'status' => 0,
            'error' => '',
            'data' => [
                [
                    'status' => null,
                    'id' => self::TEST_MESSAGE_ID_1,
                    'msisdn' => self::TEST_TARGET_1,
                    'price' => 0.5,
                ]
            ],

        ];

        $this->mock->append(new Response(200, [], json_encode($responseApiData)));

        $request = (new VoiceOTPRequest())
            ->setCode(1234)
            ->setPhone(self::TEST_TARGET_1)
            ->setSender(self::TEST_SENDER);


        $response = $this->client->execute($request);
        $requestInfo = $this->getLastRequest();
        $requestBody = $this->getRequestBody($requestInfo);
        $this->assertRequestMethod($requestInfo);
        $this->assertRequestPath('/outbox/send/json', $requestInfo);

        $this->assertEquals('callpass', $requestBody['type']);
        $this->assertEquals(1234, $requestBody['message']);
        $this->assertEquals(self::TEST_TARGET_1, $requestBody['target']);
        $this->assertEquals(self::TEST_SENDER, $requestBody['sender']);
        $this->assertRequestAuthByToken($requestBody);
        $this->assertResponseData($responseData, $response);
    }

    public function testSuccessfulStatusSending()
    {
        $responseApiData = [
            'statuses' => [
                [
                    'message_id' => 11222,
                    'country' => 'страна/оператор',
                    'status' => 12,
                    'status_desc' => 'delivered',
                    'type' => 'sms',
                    'method' => 'sms',
                    'time_seen' => "",
                ], [
                    'message_id' => 11223,
                    'country' => 'страна/оператор',
                    'status' => 18,
                    'status_desc' => 'rejected',
                    'type' => 'viber',
                    'method' => 'viber',
                    'time_seen' => '2100-03-04 22:45:32'
                ]
            ],
        ];


        $responseData = [
            'success' => true,
            'status' => null,
            'error' => '',
            'data' => [
                [
                    'message_id' => 11222,
                    'country' => 'страна/оператор',
                    'status' => 12,
                    'status_desc' => 'delivered',
                    'type' => 'sms',
                    'method' => 'sms',
                    'time_seen' => "",
                ], [
                    'message_id' => 11223,
                    'country' => 'страна/оператор',
                    'status' => 18,
                    'status_desc' => 'rejected',
                    'type' => 'viber',
                    'method' => 'viber',
                    'time_seen' => '2100-03-04 22:45:32'
                ]
            ],

        ];

        $this->mock->append(new Response(200, [], json_encode($responseApiData)));

        $request = new StatusRequest([11222, 11223]);


        $response = $this->client->execute($request);
        $requestInfo = $this->getLastRequest();
        $requestBody = $this->getRequestBody($requestInfo);
        $this->assertRequestMethod($requestInfo);
        $this->assertRequestPath('/outbox/getstatus/json', $requestInfo);
        $this->assertCount(2, $requestBody['message_ids']);
        $this->assertRequestAuthByToken($requestBody);
        $this->assertResponseData($responseData, $response);
    }


    private function getLastRequest(): object
    {
        return end($this->container)['request'];
    }

    private function getRequestBody(object $request): array
    {
        return json_decode($request->getBody()->getContents(), true);
    }

    private function assertRequestMethod(object $request): void
    {
        $this->assertEquals('POST', $request->getMethod());
    }

    private function assertRequestPath(string $path, object $request): void
    {
        $this->assertEquals($path, $request->getUri()->getPath());
    }

    private function assertRequestAuthByPassword(array $requestBody): void
    {
        $this->assertEquals(self::TEST_LOGIN, $requestBody['login']);
        $this->assertEquals(self::TEST_PASSWORD, $requestBody['password']);
    }

    private function assertRequestAuthByToken(array $requestBody): void
    {
        $reqSign = $requestBody['sign'] ?? '';
        unset($requestBody['sign']);
        $sign = Client::generateSignature($requestBody, self::TEST_TOKEN);
        $this->assertEquals($sign, $reqSign);
        $this->assertEquals(self::TEST_LOGIN, $requestBody['login']);
    }

    private function assertResponseData(array $expectedData, object $response): void
    {
        $this->assertEquals($expectedData, $response->toArray());
    }
}
