<?php

namespace TeraSMS;

use TeraSMS\Exception\RequestException;
use TeraSMS\Request\RequestInterface;
use TeraSMS\Response\Response;
use TeraSMS\Response\ResponseInterface;
use GuzzleHttp\Client as HTTPClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Client to use the terasms.ru API
 *
 * @link https://terasms.ru/api.html
 */
class Client
{
    const API_URL = 'https://auth.terasms.ru/outbox/';
    protected ClientInterface $client;
    protected string $login;
    protected string $token;
    protected ?string $password = null;

    public function __construct(string $login, string $token, ?string $password = null)
    {
        $this->login = $login;
        $this->token = $token;
        $this->password = $password;
        $this->client = new HTTPClient(
            [
                'base_uri' => self::API_URL,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]
        );
    }

    /**
     * @param ResponseInterface[]|RequestInterface $request
     * @param array                                $options
     *
     * @return ResponseInterface
     * @throws RequestException
     */
    public function execute($request, array $options = []): ResponseInterface
    {
        try {
            $options = array_merge(
                ['json' => $this->prepareRequestData($request)],
                $options
            );
            $response = $this->client->post($request->getUri(), $options);
            return Response::fromJson($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new RequestException(
                $request,
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public static function generateSignature(array $params, string $token): string
    {
        $paramsString = self::buildParamsString($params);
        return md5($paramsString . $token);
    }

    /**
     * @param RequestInterface $request
     *
     * @return array
     */
    protected function prepareRequestData(RequestInterface $request): array
    {
        $data = $request->toArray();
        $data['login'] = $this->login;
        if ($this->token) {
            $data['sign'] = self::generateSignature($data, $this->token);
        } else {
            $data['password'] = $this->password;
        }
        return $data;
    }

    private static function buildParamsString(array $params): string
    {
        $result = [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $result[] = self::buildParamsString($value);
            } else {
                $result[] = "{$key}={$value}";
            }
        }
        sort($result);
        return implode('', $result);
    }
}
