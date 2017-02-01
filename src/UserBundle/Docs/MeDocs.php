<?php

namespace UserBundle\Docs;

use CoreBundle\Docs\Docs;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Form\UserEditType;

interface MeDocs extends Docs
{
    const SECTION = 'Me';
    const HEADERS = Docs::AUTH_HEADERS;

    const DEFAULT_OUTPUT = [
        'class'     => User::class,
        'parsers'   => self::OUTPUT_PARSER,
        'groups'    => ['me'],
    ];

    const DEFAULT_INPUT  = [
        'class'      => UserEditType::class,
        'parsers'    => self::INPUT_PARSER,
    ];

    const DEFAULT = [
        'section'        => self::SECTION,
        'authentication' => true,
        'resource'       => true,
        'headers'        => self::HEADERS,
    ];


    const GET = [
        'default' => self::DEFAULT,
        'output'  => self::DEFAULT_OUTPUT,
        'statusCodes' => [
            Response::HTTP_OK           => self::HTTP_OK,
            Response::HTTP_UNAUTHORIZED => self::HTTP_UNAUTHORIZED
        ],
    ];

    const PATCH = [
        'default' => self::DEFAULT,
        'output'  => self::DEFAULT_OUTPUT,
        'input'   => self::DEFAULT_INPUT,
        'statusCodes' => [
            Response::HTTP_OK           => self::HTTP_OK,
            Response::HTTP_BAD_REQUEST  => self::HTTP_BAD_REQUEST,
            Response::HTTP_UNAUTHORIZED => self::HTTP_UNAUTHORIZED
        ],
    ];

    const DELETE = [
        'default' => self::DEFAULT,
        'statusCodes' => [
            Response::HTTP_NO_CONTENT   => self::HTTP_NO_CONTENT,
            Response::HTTP_UNAUTHORIZED => self::HTTP_UNAUTHORIZED
        ],
    ];
}
