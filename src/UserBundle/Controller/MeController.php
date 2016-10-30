<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use CoreBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Docs\MeDocs;

/**
 * Class MeController
 *
 * @package UserBundle\Controller
 *
 * @FOSRest\Version("1.0")
 *
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 */
class MeController extends AbstractUserController implements MeDocs
{
    /**
     * Return the current user
     *
     * @ApiDoc(MeDocs::GET)
     *
     * @return Response
     */
    public function getAction() : Response
    {
        return $this->forward('UserBundle:User:get', [
            'user_id' => $this->getUser()->getId(),
        ]);
    }

    /**
     * Update the current user
     *
     * @ApiDoc(MeDocs::PATCH)
     *
     * @return Response
     */
    public function patchAction() : Response
    {
        return $this->forward('UserBundle:User:patch', [
            'user_id' => $this->getUser()->getId(),
        ]);
    }

    /**
     * Delete the current user
     *
     * @ApiDoc(MeDocs::DELETE)
     *
     * @return Response
     */
    public function deleteAction() : Response
    {
        return $this->forward('UserBundle:User:delete', [
            'user_id' => $this->getUser()->getId(),
        ]);
    }
}
