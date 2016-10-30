<?php

namespace Tests\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tests\CoreBundle\Controller\AbstractControllerTest;

class MeControllerTest extends AbstractControllerTest
{
    const PREFIX_URL = '/me';

    const NEW_PASSWORD = 'super-new-password';

    private static $user = ['username' => 'tmpuser', 'email' => 'tmp-user@localhost.tld', 'password' => 'hello'];

    // === SETUP ===
    public static function setUpBeforeClass()
    {
        $container = static::createClient()->getContainer();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');

        $user = $userManager->createUser();

        $user
            ->setEmail(self::$user['email'])
            ->setUsername(self::$user['username'])
            ->setPlainPassword(self::$user['password'])
            ->setEnabled(true)
        ;

        $userManager->updateUser($user, true);
    }

    // === GET ===
    public function testGetMeSuccessful()
    {
        $header = $this->getHeaderConnect(self::USER1['username'], self::USER1['password']);

        $this->isSuccessful(Request::METHOD_GET, self::PREFIX_URL, [], $header);
    }

    public function testGetMeUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_GET, self::PREFIX_URL);
    }

    // === PATCH ===
    public function testPatchMeSuccessful()
    {
        $header = $this->getHeaderConnect(self::$user['email'], self::$user['password']);

        $params = [
            'password' => self::NEW_PASSWORD,
        ];

        $this->isSuccessful(Request::METHOD_PATCH, self::PREFIX_URL, $params, $header);

        $header = $this->getHeaderConnect(self::$user['email'], self::NEW_PASSWORD, true);

        $this->assertTrue(!is_null($header));
    }

    public function testPatchMeBadRequest()
    {
        $header = $this->getHeaderConnect(self::$user['email'], self::NEW_PASSWORD);

        $this->isBadRequest(Request::METHOD_PATCH, self::PREFIX_URL, [], $header);
    }

    public function testPatchMeUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_PATCH, self::PREFIX_URL);
    }

    // === DELETE ===
    public function testDeleteMeSuccessful()
    {
        $header = $this->getHeaderConnect(self::$user['email'], self::NEW_PASSWORD);

        $this->isSuccessful(Request::METHOD_DELETE, self::PREFIX_URL, [], $header);

        $this->isUnauthorized(Request::METHOD_GET, self::PREFIX_URL, [], $header);
    }

    public function testDeleteMeUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_DELETE, self::PREFIX_URL);
    }
}
