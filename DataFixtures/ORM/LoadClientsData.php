<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 04/12/15
 * Time: 22:50
 */

namespace Vss\OAuthExtensionBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadClientsData implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $clientManager = $this->container->get('fos_oauth_server.client_manager.default');

        $client = $clientManager->createClient();
        $client->setRedirectUris([""]);
        $client->setAllowedGrantTypes(["http://oauth.vss.com/grants/role", "refresh_token"]);
        $client->setRandomId("admin");
        $client->setSecret("sososecret!");
        $client->setId(2);

        $metadata = $manager->getClassMetaData(get_class($client));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);


        $clientManager->updateClient($client);



        $client = $clientManager->createClient();
        $client->setRedirectUris([""]);
        $client->setAllowedGrantTypes(["http://buddyvote.fr/grants/facebook", "password", "refresh_token"]);


        $client->setRandomId("client");
        $client->setSecret("notsecret");
        $client->setId(1);
        $clientManager->updateClient($client);

    }
}