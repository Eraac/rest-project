<?php

namespace LKE\UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller
{
    /**
     * @View(serializerGroups={"Default", "details-user"})
     */
    public function getUsersAction()
    {
        $users = $this->getDoctrine()->getRepository('LKEUserBundle:User')->findAll();
        
        return $users;
    }
    
    /**
     * @View(serializerGroups={"Default", "details-user"})
     */
    public function getUserAction($username)
    {
        $user = $this->getDoctrine()->getRepository('LKEUserBundle:User')->findOneByUsername($username);

        if(!is_object($user)){
          throw $this->createNotFoundException();
        }
        
        return $user;
    }
}
