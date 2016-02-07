<?php
namespace Vss\OAuthExtensionBundle\Test\DependencyInjection;

use Vss\OAuthExtensionBundle\DependencyInjection\VssOAuthExtensionExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 07/02/16
 * Time: 16:38
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function testEmptyConfig() {

        $extension = new VssOAuthExtensionExtension();
        $this->assertEquals("vss_oauth_extension", $extension->getAlias());

        $config = $this->getEmptyConfig();
        $extension->load([$config], $this->containerBuilder);

    }

    public function testFullConfig() {
        $extension = new VssOAuthExtensionExtension();
        $config = $this->getFullConfig();
        $extension->load([$config], $this->containerBuilder);
    }

    protected function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();
    }

    protected function getEmptyConfig()
    {
        $yaml = "";
        $parser = new Parser();

        return $parser->parse($yaml);
    }
    protected function getFullConfig()
    {
        $yaml = <<<EOF
auth:
    role:
        client_id: "client_id"
        client_secret: "client_secret"
        endpoint: "/token"
        logout_path: "logout_path"
    another:
        client_id: "client_id"
        client_secret: "client_secret"
        endpoint: "/token"
        logout_path: "logout_path"
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

}