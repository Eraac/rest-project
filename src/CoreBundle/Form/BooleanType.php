<?php

namespace CoreBundle\Form;

use CoreBundle\Form\DataTransformer\BooleanTransformer;
use Symfony\Component\Form\AbstractType as Base;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanType extends Base
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(
            new BooleanTransformer()
        );
    }

    /**
     * @return string
     */
    public function getParent() : string
    {
        return TextType::class;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            "invalid_message" => "core.error.invalid_boolean",
        ]);
    }
}
