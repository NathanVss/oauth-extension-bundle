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
 * Class ListClientsCommand
 * @package NP\OAuthBundle\Command
 */
class ListClientsCommand extends ContainerAwareCommand {

    /**
     *
     */
    protected function configure() {
        $this->setName('vss:oauth:list-clients')->setDescription('List oauth clients.')
            ->addOption('json', null, InputOption::VALUE_NONE, 'If set, output the clients into json.');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output) {

        $clients = $this->getContainer()->get('doctrine')->getRepository('OAuthBundle:Client')->findAll();

        $buffer = [];
        if (!$clients) {
            $output->writeln("No clients found.");
        }
        foreach ($clients as $client) {

            $clientId = $client->getId() . "_" . $client->getRandomId();
            $clientSecret = $client->getSecret();

            if ($input->getOption('json')) {
                $buffer[] = [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret
                ];
            } else {
                $output->writeln("ClientId : $clientId");
                $output->writeln("ClientSecret : $clientSecret");
                $output->writeln("####################");
            }

        }
        if ($input->getOption('json')) {
            $output->writeln(json_encode($buffer));
        }
    }
}