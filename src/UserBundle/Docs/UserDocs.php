<?php

namespace UserBundle\Docs;

use CoreBundle\Docs\Docs;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Form\UserEditType;
use UserBundle\Form\UserType;

interface UserDocs extends Docs
{
    const SECTION = 'User';
    const HEADERS = Docs::AUTH_HEADERS;

    const DEFAULT_OUTPUT = [
        'class'     => User::class,
        'parsers'   => self::OUTPUT_PARSER,
        'groups'    => ['default'],
    ];

    const DEFAULT_INPUT = [
        'class'   => UserEditType::class,
        'parsers' => self::INPUT_PARSER,
    ];

    const DEFAULT_REQUIREMENTS = [
        ['name' => 'user_id', 'dataType' => 'integer', 'description' => 'id of the user', 'requirement' => 'A valid user id']
    ];

    const DEFAULT = [
        'section'        => self::SECTION,
        'authentication' => true,
        'resource'       => true,
        'headers'        => self::HEADERS,
    ];


    const CGET = [
        'default' => self::DEFAULT,
        'output'  => [
            'class'     => User::class,
            'parsers'   => self::OUTPUT_PARSER,
            'groups'    => ['Default', 'user-list'],
        ],
        'statusCodes' => [
            Response::HTTP_OK           => self::HTTP_OK,
            Response::HTTP_BAD_REQUEST  => self::HTTP_BAD_REQUEST,
            Response::HTTP_UNAUTHORIZED => self::HTTP_UNAUTHORIZED
        ],
        'filters' => [
            ['name' => 'filter[username]', 'dataType' => 'string', 'description' => 'Search by username'],
            ['name' => 'filter[_order][id]', 'pattern' => '(ASC|DESC)', 'description' => 'Order by id'],
            ['name' => 'filter[_order][username]', 'pattern' => '(ASC|DESC)', 'description' => 'Order by username'],
            self::FILTER_CREATED_BEFORE,
            self::FILTER_CREATED_AFTER,
            self::FILTER_PAGINATION_PAGE,
            self::FILTER_PAGINATION_LIMIT,
        ],
    ];

    const GET = [
        'default' => self::DEFAULT,
        'output'  => self::DEFAULT_OUTPUT,
        'requirements' => self::DEFAULT_REQUIREMENTS,
        'statusCodes'  => [
            Response::HTTP_OK           => self::HTTP_OK,
            Response::HTTP_BAD_REQUEST  => self::HTTP_BAD_REQUEST,
            Response::HTTP_UNAUTHORIZED => self::HTTP_UNAUTHORIZED,
            Response::HTTP_NOT_FOUND    => self::HTTP_NOT_FOUND,
        ],
    ];

    const POST = [
        'default'   => self::DEFAULT,
        'headers'   => self::DEFAULT_HEADERS,
        'authentication' => false,
        'output'    => self::DEFAULT_OUTPUT,
        'input'     => [
            'class'   => UserType::class,
            'parsers' => self::INPUT_PARSER,
        ],
        'statusCodes' => [
            Response::HTTP_CREATED      => self::HTTP_CREATED,
            Response::HTTP_BAD_REQUEST  => self::HTTP_BAD_REQUEST,
        ],
    ];

    const PATCH = [
        'default'       => self::DEFAULT,
        'requirements'  => self::DEFAULT_REQUIREMENTS,
        'output'        => self::DEFAULT_OUTPUT,
        'input'         => self::DEFAULT_INPUT,
        'statusCodes'   => [
            Response::HTTP_OK           => self::HTTP_OK,
            Response::HTTP_BAD_REQUEST  => self::HTTP_BAD_REQUEST,
            Response::HTTP_UNAUTHORIZED => self::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN    => self::HTTP_FORBIDDEN,
            Response::HTTP_NOT_FOUND    => self::HTTP_NOT_FOUND,
        ],
    ];

    const DELETE = [
        'default'       => self::DEFAULT,
        'requirements'  => self::DEFAULT_REQUIREMENTS,
        'statusCodes' => [
            Response::HTTP_NO_CONTENT   => self::HTTP_NO_CONTENT,
            Response::HTTP_UNAUTHORIZED => self::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN    => self::HTTP_FORBIDDEN,
            Response::HTTP_NOT_FOUND    => self::HTTP_NOT_FOUND,
        ],
    ];

    const CONFIRM = [
        'default'        => self::DEFAULT,
        'headers'        => self::DEFAULT_HEADERS,
        'authentication' => false,
        'requirements'   => [
            ['name' => 'token', 'dataType' => 'string', 'description' => 'Token send by email', 'requirement' => 'A valid token'],
        ],
        'statusCodes' => [
            Response::HTTP_NO_CONTENT   => self::HTTP_NO_CONTENT,
            Response::HTTP_NOT_FOUND    => self::HTTP_NOT_FOUND,
        ],
    ];

    const FORGET = [
        'default'        => self::DEFAULT,
        'headers'        => self::DEFAULT_HEADERS,
        'authentication' => false,
        'parameters'   => [
            ['name' => 'email', 'format' => '{email address}', 'dataType' => 'string', 'description' => 'Email of the user', 'requirement' => 'A valid email', 'required' => true],
        ],
        'statusCodes' => [
            Response::HTTP_NO_CONTENT   => self::HTTP_NO_CONTENT,
            Response::HTTP_BAD_REQUEST  => self::HTTP_BAD_REQUEST,
            Response::HTTP_NOT_FOUND    => self::HTTP_NOT_FOUND,
        ],
    ];

    const RESET = [
        'default'        => self::DEFAULT,
        'headers'        => self::DEFAULT_HEADERS,
        'authentication' => false,
        'input'          => self::DEFAULT_INPUT,
        'output'         => [
            'class'     => User::class,
            'parsers'   => self::OUTPUT_PARSER,
            'groups'    => ['default', 'me'],
        ],
        'requirements'   => [
            ['name' => 'token', 'dataType' => 'string', 'description' => 'Token send by email', 'requirement' => 'A valid token'],
        ],
        'statusCodes' => [
            Response::HTTP_OK           => self::HTTP_OK,
            Response::HTTP_BAD_REQUEST  => self::HTTP_BAD_REQUEST,
            Response::HTTP_NOT_FOUND    => self::HTTP_NOT_FOUND,
            Response::HTTP_GONE         => "Returned when token has expired",
        ],
    ];
}
