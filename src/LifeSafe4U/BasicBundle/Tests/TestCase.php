<?php
/**
 * @author Sergey Palamarchuk
 * @email pase@ciklum.com
 */

namespace LifeSafe4U\BasicBundle\Tests;

require_once __DIR__ . '/../../../../app/AppKernel.php';

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\HttpKernel\AppKernel
     */
    protected $kernel;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    protected $entity;

    public function setUp()
    {
        // Boot the AppKernel in the test environment and with the debug.
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

        // Store the container and the entity manager in test case properties
        $this->container = $this->kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getEntityManager();

        parent::setUp();
    }

    public function tearDown()
    {
        // Shutdown the kernel.
        $this->kernel->shutdown();

        parent::tearDown();
    }

    /**
     * Helper for testing setters and getters
     * @param mixed $method
     * @param mixed $value
     * @param mixed $defaultValue
     */
    protected function checkField($method, $value, $defaultValue = null)
    {
        if (is_string($method) && preg_match('/^test/', $method)) {
            $setter = "set" . str_replace('test', '', $method);
            $getter = "get" . str_replace('test', '', $method);
        } elseif (is_array($method)) {
            list($setter, $getter) = $method;
        } else {
            throw new \Exception('Setters and Getters are not properly defined');
        }
        if ($defaultValue === true || $defaultValue === false) {
            $assert = ($defaultValue) ? 'assertTrue' : 'assertFalse';
            $this->$assert($this->entity->$getter());
        } elseif (preg_match('/instanceof/', $defaultValue)) {
            $defaultValue = str_replace('instanceof', '', $defaultValue);
            $this->assertInstanceOf(trim($defaultValue), $this->entity->$getter());
        } elseif (null !== $defaultValue) {
            $this->assertEquals($defaultValue, $this->entity->$getter());
        }
        $this->entity->$setter($value);
        $this->assertEquals($value, $this->entity->$getter());
        if (is_object($value)) {
            $this->assertInstanceOf(get_class($value), $this->entity->$getter());
        }

        return $this->entity;
    }
}
