<?php
/**
 * @author Sergey Palamarchuk
 * @email pase@ciklum.com
 */

namespace LifeSafe4U\BasicBundle\Tests;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected static $client;

    public static function setUpBeforeClass()
    {
        if (null === static::$client) {
            static::$client = static::createClient();
        }
        $application = new Application(static::$kernel);
        $application->setAutoExit(false);
        self::runConsole($application, 'doctrine:schema:update', ['--force' => true]);
        self::runConsole($application, 'cache:warmup');
    }

    protected static function createClient(array $options = [], array $server = [])
    {
        $options['debug'] = false;
        return parent::createClient($options, $server);
    }

    protected static function runConsole(\Symfony\Bundle\FrameworkBundle\Console\Application $application, $command, array $options = [])
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));

        $input = new ArrayInput($options);
        $input->setInteractive(false);

        $application->run($input);
    }
}
