<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Form\UserType;
use UserBundle\Form\UserEditType;

class UserController extends AbstractUserController
{
    /**
     * Add an user
     *
     * @param Request $request
     *
     * @return object|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @FOSRest\View(statusCode=JsonResponse::HTTP_CREATED)
     */
    public function postAction(Request $request)
    {
        $userManager = $this->getUserManager();

        $user = $userManager->createUser();

        return $this->form($request, UserType::class, $user, Request::METHOD_POST);
    }

    /**
     * Confirm an email
     *
     * @param string $token
     *
     * @FOSRest\Post("/users/confirm/{token}")
     * @FOSRest\View(statusCode=JsonResponse::HTTP_NO_CONTENT)
     */
    public function confirmEmailAction($token)
    {
        $user = $this->findUserByToken($token);

        $user->setConfirmationToken(null);
        $user->setConfirmed(true);

        $this->updateUser($user);
    }

    /**
     * Forget password
     *
     * @param Request $request
     *
     * @return null|JsonResponse
     *
     * @FOSRest\Post("/users/forget-password")
     * @FOSRest\View(statusCode=JsonResponse::HTTP_NO_CONTENT)
     */
    public function forgetPasswordAction(Request $request)
    {
        $username = $request->request->get('username');

        if (is_null($username)) {
            return new JsonResponse(['error' => $this->t('user.error.forget_password.empty_username')], JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var $user User */
        $user = $this->get('fos_user.user_manager')->findUserByUsernameOrEmail($username);

        if (null === $user) {
            throw $this->createNotFoundException(
                $this->t('user.error.forget_password.not_found')
            );
        }

        if ($user->isPasswordRequestNonExpired($this->getParameter('fos_user.resetting.token_ttl'))) {
            return new JsonResponse(['error' => $this->t('resetting.password_already_requested', [], 'FOSUserBundle')], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('user.mailer')->sendResettingEmailMessage($user);

        $user->setPasswordRequestedAt(new \DateTime());

        $this->updateUser($user);
    }

    /**
     * Reset the user password
     *
     * @param Request $request
     * @param string $token
     *
     * @return null|JsonResponse
     *
     * @FOSRest\Post("/users/reset-password/{token}")
     * @FOSRest\View(statusCode=JsonResponse::HTTP_NO_CONTENT)
     */
    public function resetPasswordAction(Request $request, $token)
    {
        $user = $this->findUserByToken($token);

        if (!$user->isPasswordRequestNonExpired($this->getParameter('fos_user.resetting.token_ttl'))) {
            return new JsonResponse(['error' => $this->t('resetting.password_request_expired')], JsonResponse::HTTP_GONE);
        }

        $form = $this->createForm(UserEditType::class, $user, ['method' => 'post']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);

            $this->updateUser($user);
        }

        return new JsonResponse($this->getErrorMessages($form), JsonResponse::HTTP_BAD_REQUEST);
    }
}
