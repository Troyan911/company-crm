<?php

namespace App\Tests\Api;

use App\Entity\Company;
use App\Tests\BaseApiTest;

class CompanyApiTest extends BaseApiTest
{
    private int $companyId;

    protected function setUp(): void
    {
        parent::setUp();

        $company = new Company();
        $company->setName('Test Company ' . uniqid());

        $this->em->persist($company);
        $this->em->flush();

        $this->companyId = $company->getId();
    }

    protected function tearDown(): void
    {
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
        $this->client->request('GET', '/api/company');

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertIsArray($data);
    }

    public function testGetOne(): void
    {
        $this->client->request('GET', '/api/company/' . $this->companyId);

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function testCreate(): void
    {
        $token = $this->getJwtToken();

        $this->client->request(
            'POST',
            '/api/company',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            json_encode(['name' => 'New Company'])
        );

        $response = $this->client->getResponse();
        $this->assertResponseStatusCodeSame(201);
    }

    public function testUpdate(): void
    {
        $token = $this->getJwtToken();

        $this->client->request(
            'PUT',
            '/api/company/' . $this->companyId,
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            json_encode(['name' => 'Updated Company'])
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        $token = $this->getJwtToken();

        $this->client->request(
            'DELETE',
            '/api/company/' . $this->companyId,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]
        );

        $response = $this->client->getResponse();
        $this->assertResponseIsSuccessful();

        $deleted = $this->em->getRepository(\App\Entity\Company::class)->find($this->companyId);
        $this->assertNull($deleted);
    }

    public function testUnauthorizedAccess(): void
    {
        $this->client->setServerParameters([]);

        // GET list allowed
        $this->client->request('GET', '/api/company');
        $this->assertResponseIsSuccessful();

        // GET one allowed
        $this->client->request('GET', '/api/company/' . $this->companyId);
        $this->assertResponseIsSuccessful();

        // POST forbidden
        $this->client->request(
            'POST',
            '/api/company',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'No Auth'])
        );

        $this->assertResponseStatusCodeSame(401);

        // PUT forbidden
        $this->client->request(
            'PUT',
            '/api/company/' . $this->companyId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['name' => 'No Auth'])
        );

        $this->assertResponseStatusCodeSame(401);

        // DELETE forbidden
        $this->client->request(
            'DELETE',
            '/api/company/' . $this->companyId
        );

        $this->assertResponseStatusCodeSame(401);
    }
}
