<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class LoadUserData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin
            ->setUsername('admin')
            ->addRole('ROLE_ADMIN')
            ->setEmail('admin@localhost.tld')
            ->setPlainPassword('admin')
        ;

        $user1 = new User();
        $user1
            ->setUsername('user1')
            ->setEmail('user1@localhost.tld')
            ->setPlainPassword('userpass')
        ;

        $user2 = new User();
        $user2
            ->setUsername('user2')
            ->setEmail('user2@localhost.tld')
            ->setPlainPassword('userpass')
        ;

        $manager->persist($userAdmin);
        $manager->persist($user1);
        $manager->persist($user2);

        $manager->flush();
    }
}
