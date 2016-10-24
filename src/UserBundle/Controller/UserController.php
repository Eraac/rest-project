<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Form\UserEditAdminType;
use UserBundle\Form\UserType;
use UserBundle\Form\UserEditType;
use UserBundle\Security\UserVoter;

class UserController extends AbstractUserController
{
    /**
     * Return list of users
     *
     * @FOSRest\View(serializerGroups={"user-list"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function cgetAction()
    {
        return $this->getRepository('UserBundle:User')->findAll();
    }

    /**
     * Return an user
     *
     * @param User $u
     *
     * @return User
     *
     * @FOSRest\Get(requirements={"u"="\d+"})
     * @FOSRest\View(serializerGroups={"default"})
     *
     * @Security("is_granted('view', u)") // user is "reserved" by Security(), is remplace by current user
     */
    public function getAction(User $u) : User
    {
        $this->tryAddSerializerGroupMe($u);

        return $u;
    }

    /**
     * Add an user
     *
     * @param Request $request
     *
     * @return object|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @FOSRest\View(serializerGroups={"me"})
     */
    public function postAction(Request $request)
    {
        $userManager = $this->getUserManager();

        $user = $userManager->createUser();

        return $this->form($request, UserType::class, $user, Request::METHOD_POST);
    }

    /**
     * Edit an user
     *
     * @param Request $request
     * @param User $u
     *
     * @FOSRest\View(serializerGroups={"default"})
     *
     * @return object|\Symfony\Component\Form\Form|JsonResponse
     *
     * @Security("is_granted('edit', u)") // user is "reserved" by Security(), is remplace by current user
     */
    public function patchAction(Request $request, User $u)
    {
        $this->tryAddSerializerGroupMe($u);

        $formType = $this->isGranted('ROLE_ADMIN') ?
            UserEditAdminType::class : UserEditType::class;

        return $this->form($request, $formType, $u, Request::METHOD_PATCH);
    }

    /**
     * Remove an user
     *
     * @param User $u
     *
     * @FOSRest\View()
     *
     * @Security("is_granted('delete', u)") // user is "reserved" by Security(), is remplace by current user
     */
    public function deleteAction(User $u)
    {
        $em = $this->getManager();

        $em->remove($u);
        $em->flush();
    }

    /**
     * Confirm an email
     *
     * @param string $token
     *
     * @FOSRest\Post("/users/confirm/{token}")
     * @FOSRest\View(statusCode=JsonResponse::HTTP_NO_CONTENT)
     */
    public function confirmEmailAction(string $token)
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
    public function resetPasswordAction(Request $request, string $token)
    {
        $user = $this->findUserByToken($token);

        if (!$user->isPasswordRequestNonExpired($this->getParameter('fos_user.resetting.token_ttl'))) {
            return new JsonResponse(['error' => $this->t('resetting.password_request_expired')], JsonResponse::HTTP_GONE);
        }

        $form = $this->createForm(UserEditType::class, $user, ['method' => Request::METHOD_POST]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setConfirmationToken(null);
            $user->setPasswordRequestedAt(null);

            $this->updateUser($user);
        }

        // avoid return 204 when json is empty
        if (empty(json_decode($request->getContent(), true))) {
            return new JsonResponse(['errors' => [$this->t('core.error.empty_json')]], JsonResponse::HTTP_BAD_REQUEST);
        }

        return $form;
    }

    /**
     * Return true if $user and current user is the same
     *
     * @param User $user
     *
     * @return bool
     */
    protected function isHimself(User $user) : bool
    {
        return $this->isGranted(UserVoter::HIMSELF, $user);
    }

    /**
     * Add serializer group "me" if the current user and $user is the same
     *
     * @param User $user
     */
    protected function tryAddSerializerGroupMe(User $user)
    {
        if ($this->isHimself($user)) {
            $this->addSerializerGroup("me");
        }
    }
}
