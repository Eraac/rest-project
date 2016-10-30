<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractControllerTest extends WebTestCase
{
    const ADMIN = ['id' => 1, 'username' => 'admin', 'email' => 'admin@localhost.tld', 'password' => 'admin'];
    const USER1 = ['id' => 2, 'username' => 'user1', 'email' => 'user1@localhost.tld', 'password' => 'userpass'];
    const USER2 = ['id' => 3, 'username' => 'user2', 'email' => 'user2@localhost.tld', 'password' => 'userpass'];

    const CLIENT_ID = '1_123';
    const CLIENT_SECRET = '456';

    const API_VERSION = '1.0';

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    private function request(string $method, string $url, array $params = [], array $headers = [])
    {
        if (is_null($this->client)) {
            $this->client = static::createClient();
        }

        $headers['HTTP_X_ACCEPT_VERSION'] = self::API_VERSION;

        $this->client->request($method, $url, $params, [], $headers);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    protected function isSuccessful(string $method, string $url, array $params = [], array $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertTrue($this->client->getResponse()->isSuccessful(),
            sprintf('Status code is %d instead of 2xx', $this->client->getResponse()->getStatusCode())
        );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    protected function isNotFound(string $method, string $url, array $params = [], array $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertTrue(
            $this->client->getResponse()->isNotFound(),
            sprintf('Status code is %s instead of 404', $this->client->getResponse()->getStatusCode())
        );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    protected function isBadRequest(string $method, string $url, array $params = [], array $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     */
    protected function isUnauthorized(string $method, string $url, array $params = [], array $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     */
    protected function isForbidden(string $method, string $url, array $params = [], array $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertTrue(
            $this->client->getResponse()->isForbidden(),
            sprintf('Status code is %s instead of 403', $this->client->getResponse()->getStatusCode())
        );
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $params
     */
    protected function isConflict(string $method, string $url, array $params = [], array $headers = [])
    {
        $this->request($method, $url, $params, $headers);

        $this->assertEquals(Response::HTTP_CONFLICT, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @param string $key
     *
     * @return array
     */
    protected function getResponseContent(string $key = null)
    {
        $json = json_decode($this->client->getResponse()->getContent(), true);

        if (is_null($json)) {
            $this->assertTrue(false, 'Json is invalid');
            return null;
        }

        return $json[$key] ?? $json;
    }

    /**
     * @param string $username
     * @param string $password
     * @return array|null
     */
    protected function getHeaderConnect(string $username, string $password, bool $checkUser = true)
    {
        $this->request(Request::METHOD_POST, '/oauth/v2/token', [
            'username' => $username, 'password' => $password,
            'grant_type' => 'password', 'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET
        ]);

        $response = $this->getResponseContent();

        if (Response::HTTP_OK != $this->client->getResponse()->getStatusCode()) {
            if ($checkUser) {
                $this->assertTrue(false, 'Bad credential');
            }

            return null;
        }

        return ['HTTP_AUTHORIZATION' => 'Bearer ' . $response['access_token']];
    }
}
