<?php

namespace CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class BooleanTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function transform($value) : bool
    {
        return $value;
    }

    /**
     * @param mixed $value
     *
     * @throws TransformationFailedException
     *
     * @return bool
     */
    public function reverseTransform($value) : bool
    {
        if ($value !== "" && !in_array($value, ['0', '1'], true)) {
            throw new TransformationFailedException(sprintf(
                "value '%s' is not a valid boolean", $value
            ));
        }

        return $value ? true : false;
    }
}
