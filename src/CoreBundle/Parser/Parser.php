<?php

namespace CoreBundle\Parser;

use CoreBundle\Form\Type\BooleanType;
use Nelmio\ApiDocBundle\Parser\ValidationParser;
use Nelmio\ApiDocBundle\Parser\FormTypeParser;
use Nelmio\ApiDocBundle\Parser\ParserInterface;
use Nelmio\ApiDocBundle\Util\DocCommentExtractor;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;

class Parser implements ParserInterface
{
    /**
     * @var FormTypeParser
     */
    protected $jmsMetadataParser;

    /**
     * @var ValidationParser
     */
    protected $validationParser;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var DocCommentExtractor
     */
    protected $docCommentExtractor;


    /**
     * CustomParser constructor.
     *
     * @param FormTypeParser $jmsMetadataParser
     * @param ValidationParser $validationParser
     * @param FormFactoryInterface $formFactory
     * @param DocCommentExtractor $docCommentExtractor
     */
    public function __construct (
        FormTypeParser $jmsMetadataParser,
        ValidationParser $validationParser,
        FormFactoryInterface $formFactory,
        DocCommentExtractor $docCommentExtractor
    ) {
        $this->jmsMetadataParser   = $jmsMetadataParser;
        $this->validationParser    = $validationParser;
        $this->formFactory         = $formFactory;
        $this->docCommentExtractor = $docCommentExtractor;
    }

    /**
     * @param array $items
     *
     * @return bool
     */
    public function supports(array $items)
    {
        return $this->jmsMetadataParser->supports($items) && $this->validationParser->supports($items);
    }

    /**
     * {@inheritdoc}
     */
    public function parse(array $input)
    {
        $formTypeOutput = $this->jmsMetadataParser->parse($input);

        $formType = $this->formFactory->create($input['class'], null, $input['options']);
        $input['class'] = $formType->getConfig()->getOption('data_class');
        $entityMetadata = $this->getEntityMetadata($input['class']);

        $validationOutput = $this->validationParser->parse($input);

        /** @var Form $item */
        foreach ($formType->all() as $item) {
            $key = $item->getName();

            $camelCasedKey = $item->getConfig()->getOption('property_path') ?: $key;

            if (!isset($validationOutput[$camelCasedKey])) {
                continue;
            }

            $format = $this->getFormat($item, $validationOutput, $camelCasedKey);
            $dataType = $this->getType($item, $validationOutput, $camelCasedKey);

            $formTypeOutput[$key]['description'] = $this->getDescription($entityMetadata->getProperty($camelCasedKey));
            $formTypeOutput[$key]['format']      = $format;
            $formTypeOutput[$key]['required']    = $validationOutput[$camelCasedKey]['required'];
            $formTypeOutput[$key]['dataType']    = $dataType;
        }

        return $formTypeOutput;
    }

    /**
     * @param $class
     *
     * @return \ReflectionClass
     */
    public function getEntityMetadata($class)
    {
        return new \ReflectionClass($class);
    }

    /**
     * @param $propertyMetadata
     *
     * @return string
     */
    private function getDescription($propertyMetadata) : string
    {
        return $this->docCommentExtractor->getDocCommentText($propertyMetadata);
    }

    /**
     * @param Form $item
     * @param array $validationOutput
     * @param $camelCasedKey
     *
     * @return null|string
     */
    private function getType(Form $item, array $validationOutput, $camelCasedKey)
    {
        $type = $item->getConfig()->getType()->getInnerType();

        if (BooleanType::class === $type) {
            return 'boolean';
        }

        return isset($validationOutput[$camelCasedKey]['dataType']) ?
            $validationOutput[$camelCasedKey]['dataType'] : null;
    }

    /**
     * @param Form $item
     * @param array $validationOutput
     * @param $camelCasedKey
     *
     * @return null|string
     */
    private function getFormat(Form $item, array $validationOutput, $camelCasedKey)
    {
        $type = $item->getConfig()->getType()->getInnerType();

        if (BooleanType::class === $type) {
            return '[false|true|0|1]';
        }

        return isset($validationOutput[$camelCasedKey]['format']) ?
            $validationOutput[$camelCasedKey]['format'] : null;
    }
}
