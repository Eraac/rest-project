<?php

namespace LKE\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CorsController extends Controller
{
    public function preflightAction()
    {
        /* Add on your vhost
         *
         * RewriteEngine on
         * RewriteCond %{REQUEST_METHOD} OPTIONS
         * RewriteCond %{REQUEST_URI} !=/preflight
         * RewriteRule ^(.*)$ /preflight [END]
         */

        $origin     = $this->getParameter("allow_origin");
        $methods    = $this->getParameter("allow_methods");
        $headers    = $this->getParameter("allow_headers");

        return new Response("", 200, [
            "Access-Control-Allow-Origin" => $origin,
            "Access-Control-Allow-Methods" => $methods,
            "Access-Control-Allow-Headers" => $headers,
        ]);
    }
}
