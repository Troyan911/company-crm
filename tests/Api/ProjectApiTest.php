<?php

namespace App\Tests\Api;

use App\Entity\Company;
use App\Entity\Project;
use App\Tests\BaseApiTest;

class ProjectApiTest extends BaseApiTest
{
    private int $companyId;
    private int $projectId;

    protected function setUp(): void
    {
        parent::setUp();

        $company = new Company();
        $company->setName('Test Company ' . uniqid());

        $this->em->persist($company);
        $this->em->flush();

        $this->companyId = $company->getId();

        $project = new Project();
        $project->setName('Test Project ' . uniqid());
        $project->setIsActive(true);
        $project->setCompany($company);

        $this->em->persist($project);
        $this->em->flush();

        $this->projectId = $project->getId();
    }

    protected function tearDown(): void
    {
        if ($this->projectId) {
            $project = $this->em->getRepository(Project::class)->find($this->projectId);
            if ($project) {
                $this->em->remove($project);
                $this->em->flush();
            }
        }

        if ($this->companyId) {
            $company = $this->em->getRepository(Company::class)->find($this->companyId);
            if ($company) {
                $this->em->remove($company);
                $this->em->flush();
            }
        }

        parent::tearDown();
    }

    public function testGetList(): void
    {
        $this->client->request('GET', '/api/project');

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testGetOne(): void
    {
        $this->client->request('GET', '/api/project/' . $this->projectId);

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('isActive', $data);

        $this->assertArrayHasKey('companyId', $data);
        $this->assertEquals($this->companyId, $data['company']['id'] ?? $data['companyId'] ?? null);
    }

    public function testCreate(): void
    {
        $token = $this->getJwtToken();

        $this->client->request(
            'POST',
            '/api/project',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ],
            json_encode([
                'name' => 'New Project',
                'isActive' => true,
                'companyId' => $this->companyId
            ])
        );

        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(201);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);

        $project = $this->em->getRepository(Project::class)->find($data['id']);
        if ($project) {
            $this->em->remove($project);
            $this->em->flush();
        }
    }

    public function testUpdate(): void
    {
        $token = $this->getJwtToken();

        $this->client->request(
            'PUT',
            '/api/project/' . $this->projectId,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ],
            json_encode([
                'name' => 'Updated Project',
                'isActive' => false,
                'companyId' => $this->companyId
            ])
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $token = $this->getJwtToken();

        $this->client->request(
            'DELETE',
            '/api/project/' . $this->projectId,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $deleted = $this->em->getRepository(Project::class)->find($this->projectId);
        $this->assertNull($deleted);
    }


    public function testUnauthorizedAccess(): void
    {
        $this->client->setServerParameters([]);

        // GET list allowed
        $this->client->request('GET', '/api/project');
        $this->assertResponseIsSuccessful();

        // GET one allowed
        $this->client->request('GET', '/api/project/' . $this->projectId);
        $this->assertResponseIsSuccessful();

        // POST forbidden
        $this->client->request(
            'POST',
            '/api/project',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'No Auth',
                'isActive' => true,
                'companyId' => 1
            ])
        );

        $this->assertResponseStatusCodeSame(401);

        // PUT forbidden
        $this->client->request(
            'PUT',
            '/api/project/' . $this->projectId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'No Auth',
                'isActive' => true,
                'companyId' => 1
            ])
        );

        $this->assertResponseStatusCodeSame(401);

        // DELETE forbidden
        $this->client->request(
            'DELETE',
            '/api/project/' . $this->projectId
        );

        $this->assertResponseStatusCodeSame(401);
    }
}
