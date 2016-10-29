<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
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
     * @ApiDoc(
     *   section = "Me",
     *   resource = true,
     *   output = {
     *      "class"= "UserBundle\Entity\User",
     *      "parsers"= {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"= {"default", "me"}
     *   },
     *   statusCodes = {
     *      Response::HTTP_OK = "Returned when is successful",
     *      Response::HTTP_UNAUTHORIZED = "Returned when you aren't authenticate"
     *   },
     * )
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
     * @ApiDoc(
     *   section = "Me",
     *   resource = true,
     *   output = {
     *      "class"= "UserBundle\Entity\User",
     *      "parsers"= {
     *          "Nelmio\ApiDocBundle\Parser\JmsMetadataParser"
     *      },
     *      "groups"= {"default", "me"}
     *   },
     *   input = {
     *      "class" = "UserBundle\Form\UserEditType",
     *      "parsers" = {
     *          "CoreBundle\Parser\Parser"
     *      },
     *   },
     *   statusCodes = {
     *      Response::HTTP_OK = "Returned when user is changed",
     *      Response::HTTP_BAD_REQUEST = "Returned when one or more parameters are invalid",
     *      Response::HTTP_UNAUTHORIZED = "Returned when you aren't authenticate",
     *   },
     * )
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
     * @ApiDoc(
     *   section = "Me",
     *   resource = true,
     *   statusCodes = {
     *      Response::HTTP_NO_CONTENT = "Returned when user is removed",
     *      Response::HTTP_UNAUTHORIZED = "Returned when you aren't authenticate",
     *   }
     * )
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
