<?php

namespace UserBundle\Controller;

use CoreBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Hateoas\Representation\PaginatedRepresentation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Docs\UserDocs;
use UserBundle\Entity\User;
use UserBundle\Form\UserEditAdminType;
use UserBundle\Form\UserType;
use UserBundle\Form\UserEditType;
use UserBundle\Security\UserVoter;

/**
 * Class UserController
 *
 * @FOSRest\Version("1.0")
 *
 * @package UserBundle\Controller
 */
class UserController extends AbstractUserController implements UserDocs
{
    /**
     * Return list of users
     *
     * @ApiDoc(UserDocs::CGET)
     *
     * @FOSRest\View(serializerGroups={"Default", "user-list"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function cgetAction(Request $request) : PaginatedRepresentation
    {
        $qb = $this->getDoctrine()->getRepository('UserBundle:User')->qbFindAll();
        $qb = $this->applyFilter('user.user_filter', $qb, $request);

        return $this->paginate($qb, $request);
    }

    /**
     * Return an user
     *
     * @param User $u
     *
     * @return User
     *
     * @ApiDoc(UserDocs::GET)
     *
     * @FOSRest\Get("/users/{user_id}", requirements={"user_id"="\d+"})
     * @FOSRest\View()
     *
     * @ParamConverter("u", class="UserBundle:User", options={"id" = "user_id"})
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
     * @ApiDoc(UserDocs::POST)
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
     * @return object|\Symfony\Component\Form\Form|JsonResponse
     *
     * @ApiDoc(UserDocs::PATCH)
     *
     * @FOSRest\Patch("/users/{user_id}", requirements={"user_id"="\d+"})
     * @FOSRest\View()
     *
     * @ParamConverter("u", class="UserBundle:User", options={"id" = "user_id"})
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
     * @ApiDoc(UserDocs::DELETE)
     *
     * @FOSRest\Delete("/users/{user_id}", requirements={"user_id"="\d+"})
     * @FOSRest\View()
     *
     * @ParamConverter("u", class="UserBundle:User", options={"id" = "user_id"})
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
     * Confirm an user
     *
     * @param string $token
     *
     * @ApiDoc(UserDocs::CONFIRM)
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
     * @ApiDoc(UserDocs::FORGET)
     *
     * @FOSRest\Post("/users/forget-password")
     * @FOSRest\View(statusCode=JsonResponse::HTTP_NO_CONTENT)
     */
    public function forgetPasswordAction(Request $request)
    {
        $email = $request->request->get('email');

        if (is_null($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => $this->t('user.error.forget_password.wrong_email')], JsonResponse::HTTP_BAD_REQUEST);
        }

        /** @var $user User */
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

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
     * @return User|JsonResponse
     *
     * @ApiDoc(UserDocs::RESET)
     *
     * @FOSRest\Post("/users/reset-password/{token}")
     * @FOSRest\View(serializerGroups={"me"})
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

            return $user;
        }

        return $this->formError($request, $form);
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
        return $this->isGranted(UserVoter::ADD_ME, $user);
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
