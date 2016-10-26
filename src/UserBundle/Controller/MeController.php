<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MeController
 *
 * @package UserBundle\Controller
 *
 * @FOSRest\Version("1.0")
 *
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 */
class MeController extends AbstractUserController
{
    /**
     * Return the current user
     *
     * @return Response
     */
    public function getAction() : Response
    {
        return $this->forward('UserBundle:User:get', [
            'u' => $this->getUser()->getId(),
        ]);
    }

    /**
     * Update the current user
     *
     * @return Response
     */
    public function patchAction() : Response
    {
        return $this->forward('UserBundle:User:patch', [
            'u' => $this->getUser()->getId(),
        ]);
    }

    /**
     * Delete the current user
     *
     * @return Response
     */
    public function deleteAction() : Response
    {
        return $this->forward('UserBundle:User:delete', [
            'u' => $this->getUser()->getId(),
        ]);
    }
}
