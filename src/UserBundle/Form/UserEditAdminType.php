<?php

namespace UserBundle\Form;

use CoreBundle\Form\Type\AbstractApiType;
use CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use UserBundle\Entity\User;

class UserEditAdminType extends AbstractApiType
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;


    /**
     * UserEditAdminType constructor.
     *
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', BooleanType::class)
            ->add('confirmed', BooleanType::class)
        ;

        if ($this->isOtherUser($builder->getData())) {
            $builder->remove('password');
        }
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

    /**
     * @param User $editedUser
     *
     * @return bool
     */
    private function isOtherUser(User $editedUser) : bool
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if (is_null($user)) {
            return true;
        }

        return $user->getId() !== $editedUser->getId();
    }
}
