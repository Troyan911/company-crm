<?php

namespace App\Tests\Api;

use App\Entity\Project;
use App\Entity\User;
use App\Tests\BaseApiTest;

class UserApiTest extends BaseApiTest
{
    private int $projectId;
    private ?int $userId = null;

    protected function setUp(): void
    {
        parent::setUp();

        $project = new Project();
        $project->setName('Test Project ' . uniqid());
        $project->setIsActive(true);

        $this->em->persist($project);
        $this->em->flush();

        $this->projectId = $project->getId();
    }

    protected function tearDown(): void
    {
        if ($this->userId) {
            $user = $this->em->getRepository(User::class)->find($this->userId);
            if ($user) {
                $this->em->remove($user);
                $this->em->flush();
            }
        }

        if ($this->projectId) {
            $project = $this->em->getRepository(Project::class)->find($this->projectId);
            if ($project) {
                $this->em->remove($project);
                $this->em->flush();
            }
        }

        parent::tearDown();
    }

    public function testGetList(): void
    {
        $this->client->request('GET', '/api/user');

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testGetOne(): void
    {
        $user = new User();
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setEmail('testuser' . uniqid() . '@mail.com');
        //todo hash password
        $user->setPassword('12345678');
        $user->setProject($this->em->getRepository(Project::class)->find($this->projectId));

        $this->em->persist($user);
        $this->em->flush();
        $this->userId = $user->getId();

        $this->client->request('GET', '/api/user/' . $this->userId);

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('firstName', $data);
        $this->assertArrayHasKey('lastName', $data);
        $this->assertArrayHasKey('email', $data);
        $this->assertArrayHasKey('projectId', $data);
        $this->assertEquals($this->projectId, $data['projectId']);
    }

    public function testCreate(): void
    {
        $token = $this->getJwtToken();

        $this->client->request(
            'POST',
            '/api/user',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ],
            json_encode([
                'firstName' => 'New',
                'lastName' => 'User',
                'email' => 'newuser' . uniqid() . '@mail.com',
                'password' => '12345678',
                'projectId' => $this->projectId
            ])
        );

        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($response->getContent(), true);
        $this->userId = $data['id'] ?? null;
    }

    public function testUpdate(): void
    {
        $user = new User();
        $user->setFirstName('Old');
        $user->setLastName('User');
        $user->setEmail('olduser' . uniqid() . '@mail.com');
        $user->setPassword('12345678');
        $user->setProject($this->em->getRepository(Project::class)->find($this->projectId));

        $this->em->persist($user);
        $this->em->flush();
        $this->userId = $user->getId();

        $token = $this->getJwtToken();

        $this->client->request(
            'PUT',
            '/api/user/' . $this->userId,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ],
            json_encode([
                'firstName' => 'Updated',
                'lastName' => 'User',
                'email' => 'updateduser' . uniqid() . '@mail.com',
                'password' => '12345678',
                'projectId' => $this->projectId
            ])
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $user = new User();
        $user->setFirstName('Delete');
        $user->setLastName('User');
        $user->setEmail('deleteuser' . uniqid() . '@mail.com');
        $user->setPassword('12345678');
        $user->setProject($this->em->getRepository(Project::class)->find($this->projectId));

        $this->em->persist($user);
        $this->em->flush();
        $this->userId = $user->getId();

        $token = $this->getJwtToken();

        $this->client->request(
            'DELETE',
            '/api/user/' . $this->userId,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $deleted = $this->em->getRepository(User::class)->find($this->userId);
        $this->assertNull($deleted);

        $this->userId = null;
    }

    public function testUnauthorizedAccess(): void
    {
        $user = new User();
        $user->setFirstName('Some');
        $user->setLastName('User');
        $user->setEmail('someuser' . uniqid() . '@mail.com');
        $user->setPassword('12345678');
        $this->em->persist($user);
        $this->em->flush();

        $this->userId = $user->getId();
        $this->client->setServerParameters([]);

        // GET list allowed
        $this->client->request('GET', '/api/user');
        $this->assertResponseIsSuccessful();

        // GET one allowed
        $this->client->request('GET', '/api/user/' . $this->userId);
        $this->assertResponseIsSuccessful();

        // POST forbidden
        $this->client->request(
            'POST',
            '/api/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => 'No',
                'lastName' => 'Auth',
                'email' => 'noauth@test.com',
                'password' => '123456',
                'projectId' => 1
            ])
        );
        $this->assertResponseStatusCodeSame(401);

        // PUT forbidden
        $this->client->request(
            'PUT',
            '/api/user/' . $this->userId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'firstName' => 'No',
                'lastName' => 'Auth',
                'email' => 'noauth@test.com',
                'password' => '123456',
                'projectId' => 1
            ])
        );
        $this->assertResponseStatusCodeSame(401);

        // DELETE forbidden
        $this->client->request('DELETE', '/api/user/' . $this->userId);
        $this->assertResponseStatusCodeSame(401);
    }
}
