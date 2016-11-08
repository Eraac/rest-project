<?php

namespace Tests\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tests\CoreBundle\Controller\AbstractControllerTest;
use UserBundle\Entity\User;

class UserControllerTest extends AbstractControllerTest
{
    const PREFIX_URL = '/users';

    const NEW_USER = ['username' => 'tmp-user', 'email' => 'tmp-user@localhost.tld', 'password' => 'hello'];
    const NEW_PASSWORD = 'super-new-password';

    private static $id = 0;

    // === HELPER ===
    private function getToken(string $email) : string
    {
        $container = static::createClient()->getContainer();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');
        $user = $userManager->findUserByEmail($email);

        return $user->getConfirmationToken();
    }

    /**
     * @param string $email
     *
     * @return null|User
     */
    private function getUserFromEmail(string $email)
    {
        $container = static::createClient()->getContainer();

        /** @var \FOS\UserBundle\Doctrine\UserManager $userManager */
        $userManager = $container->get('fos_user.user_manager');

        /** @var User $user */
        $user = $userManager->findUserByEmail($email);

        return $user;
    }

    // === GET ===
    public function testGetUserSuccessful()
    {
        $this->isSuccessful(Request::METHOD_GET, self::PREFIX_URL . '/' . self::USER1['id']);
    }

    public function testGetUserNotFound()
    {
        $this->isNotFound(Request::METHOD_GET, self::PREFIX_URL . '/9876543210');
    }

    // === CGET ===
    public function testCGetUsersSuccessful()
    {
        $header = $this->getHeaderConnect(self::USER1['username'], self::USER1['password']);

        $this->isSuccessful(Request::METHOD_GET, self::PREFIX_URL, [], $header);
    }

    public function testCGetUsersUnauthorized()
    {
        $this->isUnauthorized(Request::METHOD_GET, self::PREFIX_URL);
    }

    // === POST ===
    public function testPostUsersSuccessful()
    {
        $this->isSuccessful(Request::METHOD_POST, self::PREFIX_URL, self::NEW_USER);

        self::$id = $this->getResponseContent('id');
    }

    public function testPostUsersBadRequest()
    {
        $params = [
            'username'  => 'super username',
            'email'     => 'not-an-email',
            'password'  => 'hello',
        ];

        $this->isBadRequest(Request::METHOD_POST, self::PREFIX_URL, $params);
    }

    // === POST - CONFIRM ===
    public function testPostUsersConfirmSuccessful()
    {
        $token = $this->getToken(self::NEW_USER['email']);
        $url = self::PREFIX_URL . '/confirm/' . $token;

        $this->isSuccessful(Request::METHOD_POST, $url);

        /** @var User $user */
        $user = $this->getUserFromEmail(self::NEW_USER['email']);

        $this->assertTrue($user->isConfirmed());
    }

    public function testPostUsersConfirmNotFound()
    {
        $url = self::PREFIX_URL . '/confirm/123';

        $this->isNotFound(Request::METHOD_POST, $url);
    }

    // === POST - FORGET PASSWORD ===
    public function testPostUsersForgetSuccessful()
    {
        $params = [
            'email' => self::NEW_USER['email']
        ];

        $url = self::PREFIX_URL . '/forget-password';

        $this->isSuccessful(Request::METHOD_POST, $url, $params);
    }

    public function testPostUsersForgetBadRequest()
    {
        $params = [
            'email' => 'not-an-email'
        ];

        $url = self::PREFIX_URL . '/forget-password';

        $this->isBadRequest(Request::METHOD_POST, $url, $params);
    }

    public function testPostUsersForgetNotFound()
    {
        $params = [
            'email' => 'not-an-user@localhost.tld'
        ];

        $url = self::PREFIX_URL . '/forget-password';

        $this->isNotFound(Request::METHOD_POST, $url, $params);
    }

    // === POST - RESET PASSWORD ===
    public function testPostUsersResetBadRequest()
    {
        $token = $this->getToken(self::NEW_USER['email']);
        $url = self::PREFIX_URL . '/reset-password/' . $token;

        $params = [
            'bad-field' => self::NEW_PASSWORD
        ];

        $this->isBadRequest(Request::METHOD_POST, $url, $params);
    }

    public function testPostUsersResetSuccessful()
    {
        $token = $this->getToken(self::NEW_USER['email']);
        $url = self::PREFIX_URL . '/reset-password/' . $token;

        $params = [
            'password' => self::NEW_PASSWORD
        ];

        $this->isSuccessful(Request::METHOD_POST, $url, $params);

        $this->getHeaderConnect(self::NEW_USER['email'], self::NEW_PASSWORD, true);
    }

    public function testPostUsersResetNotFound()
    {
        $url = self::PREFIX_URL . '/reset-password/123';

        $params = [
            'password' => self::NEW_PASSWORD
        ];

        $this->isNotFound(Request::METHOD_POST, $url, $params);
    }

    // === PATCH ===
    public function testPatchUsersSuccessful()
    {
        $header = $this->getHeaderConnect(self::NEW_USER['email'], self::NEW_PASSWORD);
        $url = self::PREFIX_URL . '/' . self::$id;

        $params = [
            'password' => self::NEW_PASSWORD
        ];

        $this->isSuccessful(Request::METHOD_PATCH, $url, $params, $header);
    }

    public function testPatchUsersBadRequest()
    {
        $header = $this->getHeaderConnect(self::USER1['email'], self::USER1['password']);
        $url = self::PREFIX_URL . '/' . self::USER1['id'];

        $this->isBadRequest(Request::METHOD_PATCH, $url, [], $header);
    }

    public function testPatchUsersUnauthorized()
    {
        $url = self::PREFIX_URL . '/' . self::USER1['id'];

        $this->isUnauthorized(Request::METHOD_PATCH, $url);
    }

    public function testPatchUsersForbidden()
    {
        $header = $this->getHeaderConnect(self::USER2['email'], self::USER2['password']);
        $url = self::PREFIX_URL . '/' . self::USER1['id'];

        $this->isForbidden(Request::METHOD_PATCH, $url, [], $header);
    }

    public function testPatchUsersNotFound()
    {
        $header = $this->getHeaderConnect(self::USER2['email'], self::USER2['password']);
        $url = self::PREFIX_URL . '/987654321';

        $this->isNotFound(Request::METHOD_PATCH, $url, [], $header);
    }

    // === DELETE ===
    public function testDeleteUsersSuccessful()
    {
        $header = $this->getHeaderConnect(self::NEW_USER['email'], self::NEW_PASSWORD);
        $url = self::PREFIX_URL . '/' . self::$id;

        $this->isSuccessful(Request::METHOD_DELETE, $url, [], $header);

        $header = $this->getHeaderConnect(self::ADMIN['email'], self::ADMIN['password']);

        $this->isNotFound(Request::METHOD_GET, $url, [], $header);
    }

    public function testDeleteUsersUnauthorized()
    {
        $url = self::PREFIX_URL . '/' . self::ADMIN['id'];

        $this->isUnauthorized(Request::METHOD_DELETE, $url);
    }

    public function testDeleteUsersFordibben()
    {
        $header = $this->getHeaderConnect(self::USER1['email'], self::USER1['password']);
        $url = self::PREFIX_URL . '/' . self::ADMIN['id'];

        $this->isForbidden(Request::METHOD_DELETE, $url, [], $header);
    }

    public function testDeleteUsersNotFound()
    {
        $header = $this->getHeaderConnect(self::ADMIN['email'], self::ADMIN['password']);
        $url = self::PREFIX_URL . '/' . self::$id;

        $this->isNotFound(Request::METHOD_DELETE, $url, [], $header);
    }
}
