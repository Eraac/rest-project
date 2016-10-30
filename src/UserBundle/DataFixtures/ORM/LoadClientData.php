<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\Client;

class LoadClientData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $client->setAllowedGrantTypes(['password', 'refresh_token']);
        $client->setRedirectUris([]);
        $client->setRandomId('123');
        $client->setSecret('456');

        $manager->persist($client);

        $manager->flush();
    }
}
