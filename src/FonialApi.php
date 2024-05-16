<?php

namespace Fucodo\ApiFonial;

use Fucodo\ApiFonial\Exception\AuthenticationException;
use Fucodo\ApiFonial\Exception\SessionException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class FonialApi
{
    private $_sid = false;
    private $client;
    private $requestFactory;
    private $logger;

    private const API_URL = 'https://kundenkonto.fonial.de/api/2.0';

    function __construct(ClientInterface $client, RequestFactoryInterface $requestFactory, LoggerInterface $logger) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->logger = $logger;
    }

    function deviceGet(): array {
        return $this->post(self::API_URL . '/devices/get', ['sid' => $this->_sid]);
    }

    function numbersGet(): array {
        return $this->post(self::API_URL . '/numbers/get', ['sid' => $this->_sid]);
    }

    function evnGet(string $start, string $end = ''): array {
        if (empty($end)) {
            $end = date('Y-m-d H:i:s');
        }
        return $this->post(self::API_URL . '/evn/get', ['sid' => $this->_sid, 'start' => $start, 'end' => $end]);
    }

    function journalGet(string $start, string $end = ''): array {
        if (empty($end)) {
            $end = date('Y-m-d H:i:s');
        }
        return $this->post(self::API_URL . '/journal/get', ['sid' => $this->_sid, 'start' => $start, 'end' => $end]);
    }

    function auth(string $username, string $password) {
        $session = $this->post(self::API_URL . '/session');

        if ($session['status'] !== 'ok') {
            throw new SessionException('Session error', $session);
        }

        $sid = $session['sid'];
        $this->logger->info('Session ID erhalten', ['sid' => $sid]);

        $session_authenticate = [
            'sid' => $sid,
            'username' => $username,
            'password' => $password
        ];

        $auth = $this->post(self::API_URL . '/session/authenticate', $session_authenticate);
        $this->logger->info('Authentifizierungsergebnis', ['auth' => $auth]);

        if ($auth['status'] === 'ok' && $auth['authenticated'] === '1') {
            $this->_sid = $sid;
            return true;
        }

        throw new AuthenticationException('Session authenticate error', $auth);

    }

    private function post(string $url, array $data = []): array {
        $jsonData = json_encode($data);
        $this->logger->info('Aufruf der URL', ['url' => $url]);

        $request = $this->requestFactory->createRequest('POST', $url)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(\Nyholm\Psr7\Stream::create($jsonData));

        $response = $this->client->sendRequest($request);
        return $this->handleResponse($response);
    }

    private function handleResponse(ResponseInterface $response): array {
        $body = (string)$response->getBody();
        return json_decode($body, true);
    }
}