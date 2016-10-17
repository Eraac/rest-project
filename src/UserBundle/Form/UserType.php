<?php

namespace UserBundle\Form;

use CoreBundle\Form\AbstractApiType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use UserBundle\Entity\User;

class UserType extends AbstractApiType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',   TextType::class)
            ->add('email',      EmailType::class)
            ->add('password',   PasswordType::class, ['property_path' => 'plainPassword'])
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
}
