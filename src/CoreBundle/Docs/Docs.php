<?php

namespace CoreBundle\Docs;

use CoreBundle\Parser\Parser;
use CoreBundle\Service\Paginator;
use Nelmio\ApiDocBundle\Parser\JmsMetadataParser;

interface Docs
{
    // Header
    const CONTENT_TYPE_HEADER   = ['name' => 'Content-Type', 'description' => 'Format of the request', 'required' => true, 'default' => 'application/json'];
    const ACCEPT_VERSION_HEADER = ['name' => 'X-Accept-Version', 'description' => 'Version of the api', 'required' => true, 'default' => '1.0'];
    const AUTHORIZATION_HEADER  = ['name' => 'Authorization', 'description' => 'User token', 'required' => true, 'default' => 'Bearer {token}'];

    const DEFAULT_HEADERS = [
        self::CONTENT_TYPE_HEADER,
        self::ACCEPT_VERSION_HEADER
    ];

    const AUTH_HEADERS = [
        self::CONTENT_TYPE_HEADER,
        self::ACCEPT_VERSION_HEADER,
        self::AUTHORIZATION_HEADER
    ];

    // Filter
    const FILTER_PAGINATION_PAGE  = ['name' => Paginator::PAGE,  'dataType' => 'integer', 'description' => 'Page of the collection',  'default' => '1'];
    const FILTER_PAGINATION_LIMIT = ['name' => Paginator::LIMIT, 'dataType' => 'integer', 'description' => 'Limit ot items per page', 'default' => Paginator::DEFAULT_LIMIT];
    const FILTER_CREATED_BEFORE   = ['name' => 'filter[created_before]', 'dataType' => 'integer', 'pattern' => '{unix timestamp}'];
    const FILTER_CREATED_AFTER    = ['name' => 'filter[created_after]',  'dataType' => 'integer', 'pattern' => '{unix timestamp}'];

    // Parser
    const OUTPUT_PARSER = [
        JmsMetadataParser::class,
    ];

    const INPUT_PARSER = [
        Parser::class
    ];

    // Status code
    const HTTP_OK           = 'Returned when is successful';
    const HTTP_CREATED      = 'Returned when resource is create';
    const HTTP_NO_CONTENT   = 'Returned when is successful but no content is returned';
    const HTTP_BAD_REQUEST  = 'Returned when one or more parameters are invalid';
    const HTTP_UNAUTHORIZED = 'Returned when authentication is required';
    const HTTP_FORBIDDEN    = 'Returned when you have not the necessary permissions for the resource';
    const HTTP_NOT_FOUND    = 'Returned when resource could not be found';
}
