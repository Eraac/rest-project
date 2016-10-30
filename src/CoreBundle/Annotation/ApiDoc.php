<?php

namespace CoreBundle\Annotation;

use Nelmio\ApiDocBundle\Annotation\ApiDoc as NelmioApiDoc;

/**
 * @Annotation
 */
class ApiDoc extends NelmioApiDoc
{
    /**
     * @inheritdoc
     */
    public function __construct(array $data)
    {
        if (isset($data['value'])) {
            if (isset($data['value']['default'])) {
                $data = array_merge($data['value']['default'], $data['value']);
            } else {
                $data = $data['value'];
            }
        }

        parent::__construct($data);
    }
}
