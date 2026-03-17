<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseApiTest extends WebTestCase
{
    protected ?\Symfony\Bundle\FrameworkBundle\KernelBrowser $client = null;
    protected ?EntityManagerInterface $em = null;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->client === null) {
            $this->client = static::createClient();
            $kernel = self::$kernel ?? self::$container->get('kernel');
            $this->em = $this->client->getContainer()->get('doctrine')->getManager();
        }
    }

    protected function getJwtToken(string $email = 'user1@test.com', string $password = 'secret'): string
    {
        $this->client->request(
            'POST',
            '/api/login',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => $email,
                'password' => $password,
            ])
        );

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);

        return $data['token'] ?? '';
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->em) {
            $this->em->close();
            $this->em = null;
        }

        $this->client = null;
    }
}
