<?php

namespace UserBundle\Form;

use CoreBundle\Form\AbstractApiType;
use CoreBundle\Form\BooleanType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use UserBundle\Entity\User;

class UserEditAdminType extends AbstractApiType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('password')
            ->add('enabled', BooleanType::class)
            ->add('confirmed', BooleanType::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));

        parent::configureOptions($resolver);
    }

    /**
     * @return string
     */
    public function getParent() : string
    {
        return UserEditType::class;
    }
}
