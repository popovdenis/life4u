<?php

namespace LifeSafe4U\BasicBundle\Tests\Controller;

use LifeSafe4U\BasicBundle\Tests\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::$client;

        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
