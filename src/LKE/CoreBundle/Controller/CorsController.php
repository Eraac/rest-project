<?php

namespace LKE\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CorsController extends Controller
{
    public function preflightAction()
    {
        // TODO add header instead use apache
        return new Response();
    }
}
