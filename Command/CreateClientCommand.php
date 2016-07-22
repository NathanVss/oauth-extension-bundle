<?php
/**
 * Created by PhpStorm.
 * User: Nathan
 * Date: 10/07/15
 * Time: 00:26
 */

namespace Vss\OAuthExtensionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateClientCommand
 * @package NP\OAuthBundle\Command
 */
class CreateClientCommand extends ContainerAwareCommand {

    /**
     *
     */
    protected function configure() {
        $this->setName('vss:oauth:create-client')->setDescription('Create OAuth Client.')
            ->addArgument('grants', InputArgument::REQUIRED, 'Grants');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array(''));
        $client->setAllowedGrantTypes(explode(',', $input->getArgument('grants')));
        $clientManager->updateClient($client);

        $clientId = $client->getId() . "_" . $client->getRandomId();
        $clientSecret = $client->getSecret();

        $output->writeln("New Client created.");
        $output->writeln("ClientId : $clientId");
        $output->writeln("ClientSecret : $clientSecret");
    }
}